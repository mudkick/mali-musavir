<?php

namespace Tests\Feature;

use App\Enums\BelgeDurumu;
use App\Enums\BelgeTuru;
use App\Jobs\ExtractBelgeJob;
use App\Models\Belge;
use App\Models\Mukellef;
use App\Schemas\FaturaSchema;
use App\Services\BelgeExtractionService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Structured\Response as StructuredResponse;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\ValueObjects\Usage;
use Tests\TestCase;

class BelgeExtractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_fatura_schema_has_correct_properties(): void
    {
        $schema = FaturaSchema::schema();

        $this->assertInstanceOf(ObjectSchema::class, $schema);
        $this->assertEquals('fatura', $schema->name);

        $propertyNames = array_map(fn ($prop) => $prop->name, $schema->properties);

        $expectedProperties = [
            'vkn',
            'fatura_no',
            'tarih',
            'kdv_oran',
            'kdv_tutar',
            'net_tutar',
            'toplam',
            'firma_adi',
            'aciklama',
            'belge_turu',
            'confidence',
        ];

        foreach ($expectedProperties as $property) {
            $this->assertContains($property, $propertyNames, "Schema should contain property: {$property}");
        }

        $this->assertCount(10, $schema->requiredFields, 'Schema should have 10 required fields');
    }

    public function test_vkn_checksum_validation_accepts_valid_vkn(): void
    {
        $service = new BelgeExtractionService;

        // Valid VKN: 1111111114 (calculated valid checksum)
        $data = ['vkn' => '1111111114'];
        $result = $service->validateExtraction($data);

        $this->assertTrue($result['validation_passed']);
        $this->assertEmpty($result['validation_errors']);
    }

    public function test_vkn_checksum_validation_rejects_invalid_vkn(): void
    {
        $service = new BelgeExtractionService;

        // Invalid VKN: wrong checksum (9876543210 has checksum 7, not 0)
        $data = ['vkn' => '9876543210'];
        $result = $service->validateExtraction($data);

        $this->assertFalse($result['validation_passed']);
        $this->assertContains('VKN checksum geçersiz', $result['validation_errors']);
    }

    public function test_vkn_checksum_validation_rejects_wrong_length(): void
    {
        $service = new BelgeExtractionService;

        // Too short
        $data = ['vkn' => '123456789'];
        $result = $service->validateExtraction($data);

        $this->assertFalse($result['validation_passed']);
        $this->assertContains('VKN checksum geçersiz', $result['validation_errors']);

        // Too long
        $data = ['vkn' => '12345678901'];
        $result = $service->validateExtraction($data);

        $this->assertFalse($result['validation_passed']);
        $this->assertContains('VKN checksum geçersiz', $result['validation_errors']);
    }

    public function test_tutar_cross_validation_accepts_matching_amounts(): void
    {
        $service = new BelgeExtractionService;

        $data = [
            'vkn' => '1111111114',
            'net_tutar' => 1000.00,
            'kdv_tutar' => 200.00,
            'toplam' => 1200.00,
        ];

        $result = $service->validateExtraction($data);

        $this->assertTrue($result['validation_passed']);
        $this->assertEmpty($result['validation_errors']);
    }

    public function test_tutar_cross_validation_rejects_mismatched_amounts(): void
    {
        $service = new BelgeExtractionService;

        $data = [
            'vkn' => '1111111114',
            'net_tutar' => 1000.00,
            'kdv_tutar' => 200.00,
            'toplam' => 1500.00, // Wrong total
        ];

        $result = $service->validateExtraction($data);

        $this->assertFalse($result['validation_passed']);
        $this->assertGreaterThanOrEqual(1, count($result['validation_errors']));
        $this->assertStringContainsString('Tutar uyumsuzluğu', $result['validation_errors'][0]);
    }

    public function test_tutar_cross_validation_allows_small_tolerance(): void
    {
        $service = new BelgeExtractionService;

        // 1.5% difference (within 2% tolerance)
        $data = [
            'vkn' => '1111111114',
            'net_tutar' => 1000.00,
            'kdv_tutar' => 200.00,
            'toplam' => 1218.00, // 1.5% difference
        ];

        $result = $service->validateExtraction($data);

        $this->assertTrue($result['validation_passed']);
        $this->assertEmpty($result['validation_errors']);
    }

    public function test_extract_belge_job_sets_correct_status_for_high_confidence(): void
    {
        Queue::fake();

        $mukellef = Mukellef::factory()->create([
            'telefon' => '+905551234567',
        ]);

        $belge = Belge::factory()->create([
            'mukellef_id' => $mukellef->id,
            'gorsel_path' => 'belgeler/test.jpg',
            'durum' => BelgeDurumu::Bekliyor,
        ]);

        // Mock extraction service
        $extractionService = Mockery::mock(BelgeExtractionService::class);
        $extractionService->shouldReceive('extract')
            ->once()
            ->with($belge->gorsel_path)
            ->andReturn([
                'vkn' => '1111111114',
                'fatura_no' => 'ABC123',
                'tarih' => '2026-02-10',
                'kdv_oran' => 20,
                'kdv_tutar' => 200.00,
                'net_tutar' => 1000.00,
                'toplam' => 1200.00,
                'firma_adi' => 'Test A.Ş.',
                'aciklama' => 'Test hizmet',
                'belge_turu' => 'fatura',
                'confidence' => 0.95,
            ]);

        $extractionService->shouldReceive('validateExtraction')
            ->once()
            ->andReturnUsing(function ($data) {
                return [
                    ...$data,
                    'validation_passed' => true,
                    'validation_errors' => [],
                ];
            });

        // Mock WhatsAppService
        $whatsAppService = Mockery::mock(WhatsAppService::class);
        $whatsAppService->shouldNotReceive('sendMessage');

        // Dispatch job
        $job = new ExtractBelgeJob($belge);
        $job->handle($extractionService, $whatsAppService);

        // Refresh model
        $belge->refresh();

        $this->assertEquals(BelgeDurumu::Islendi, $belge->durum);
        $this->assertEquals(0.95, $belge->confidence);
        $this->assertNotNull($belge->extracted_data);
        $this->assertEquals('1111111114', $belge->extracted_data['vkn']);
    }

    public function test_extract_belge_job_sets_kontrol_gerekli_for_low_confidence(): void
    {
        Queue::fake();

        $mukellef = Mukellef::factory()->create([
            'telefon' => '+905551234567',
        ]);

        $belge = Belge::factory()->create([
            'mukellef_id' => $mukellef->id,
            'gorsel_path' => 'belgeler/test.jpg',
            'durum' => BelgeDurumu::Bekliyor,
        ]);

        // Mock extraction service with low confidence
        $extractionService = Mockery::mock(BelgeExtractionService::class);
        $extractionService->shouldReceive('extract')
            ->once()
            ->with($belge->gorsel_path)
            ->andReturn([
                'vkn' => '1111111114',
                'fatura_no' => 'ABC123',
                'tarih' => '2026-02-10',
                'kdv_oran' => 20,
                'kdv_tutar' => 200.00,
                'net_tutar' => 1000.00,
                'toplam' => 1200.00,
                'firma_adi' => 'Test A.Ş.',
                'aciklama' => null,
                'belge_turu' => 'fatura',
                'confidence' => 0.70, // Low confidence
            ]);

        $extractionService->shouldReceive('validateExtraction')
            ->once()
            ->andReturnUsing(function ($data) {
                return [
                    ...$data,
                    'validation_passed' => true,
                    'validation_errors' => [],
                ];
            });

        // Mock WhatsAppService - should receive notification
        $whatsAppService = Mockery::mock(WhatsAppService::class);
        $whatsAppService->shouldReceive('sendMessage')
            ->once()
            ->with('+905551234567', Mockery::type('string'));

        // Dispatch job
        $job = new ExtractBelgeJob($belge);
        $job->handle($extractionService, $whatsAppService);

        // Refresh model
        $belge->refresh();

        $this->assertEquals(BelgeDurumu::KontrolGerekli, $belge->durum);
        $this->assertEquals(0.70, $belge->confidence);
    }

    public function test_extract_belge_job_sets_kontrol_gerekli_for_failed_validation(): void
    {
        Queue::fake();

        $mukellef = Mukellef::factory()->create([
            'telefon' => '+905551234567',
        ]);

        $belge = Belge::factory()->create([
            'mukellef_id' => $mukellef->id,
            'gorsel_path' => 'belgeler/test.jpg',
            'durum' => BelgeDurumu::Bekliyor,
        ]);

        // Mock extraction service with invalid VKN
        $extractionService = Mockery::mock(BelgeExtractionService::class);
        $extractionService->shouldReceive('extract')
            ->once()
            ->with($belge->gorsel_path)
            ->andReturn([
                'vkn' => '9876543210', // Invalid checksum
                'fatura_no' => 'ABC123',
                'tarih' => '2026-02-10',
                'kdv_oran' => 20,
                'kdv_tutar' => 200.00,
                'net_tutar' => 1000.00,
                'toplam' => 1200.00,
                'firma_adi' => 'Test A.Ş.',
                'aciklama' => null,
                'belge_turu' => 'fatura',
                'confidence' => 0.95,
            ]);

        $extractionService->shouldReceive('validateExtraction')
            ->once()
            ->andReturnUsing(function ($data) {
                return [
                    ...$data,
                    'validation_passed' => false,
                    'validation_errors' => ['VKN checksum geçersiz'],
                ];
            });

        // Mock WhatsAppService - should receive notification
        $whatsAppService = Mockery::mock(WhatsAppService::class);
        $whatsAppService->shouldReceive('sendMessage')
            ->once()
            ->with('+905551234567', Mockery::type('string'));

        // Dispatch job
        $job = new ExtractBelgeJob($belge);
        $job->handle($extractionService, $whatsAppService);

        // Refresh model
        $belge->refresh();

        $this->assertEquals(BelgeDurumu::KontrolGerekli, $belge->durum);
        $this->assertFalse($belge->extracted_data['validation_passed']);
    }
}
