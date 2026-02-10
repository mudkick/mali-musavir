<?php

namespace Tests\Feature;

use App\Enums\BelgeDurumu;
use App\Enums\KonfirmasyonYaniti;
use App\Jobs\ExtractBelgeJob;
use App\Models\Konfirmasyon;
use App\Models\Mukellef;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    private Mukellef $mukellef;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mukellef = Mukellef::factory()->create([
            'telefon' => '05551234567',
        ]);
    }

    public function test_unknown_number_returns_error_message(): void
    {
        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905559999999',
            'Body' => 'Merhaba',
            'NumMedia' => '0',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=utf-8');
        $response->assertSee('Tanımlanamayan numara');
    }

    public function test_image_media_creates_belge_and_dispatches_job(): void
    {
        Queue::fake();
        Storage::fake('local');
        Http::fake([
            '*' => Http::response('fake-image-content', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => '',
            'NumMedia' => '1',
            'MediaUrl0' => 'https://api.twilio.com/media/test123',
            'MediaContentType0' => 'image/jpeg',
        ]);

        $response->assertStatus(200);
        $response->assertSee('1 belge alındı');

        $this->assertDatabaseHas('belgeler', [
            'mukellef_id' => $this->mukellef->id,
            'durum' => BelgeDurumu::Bekliyor->value,
        ]);

        Queue::assertPushed(ExtractBelgeJob::class);
    }

    public function test_pdf_media_creates_belge_and_dispatches_job(): void
    {
        Queue::fake();
        Storage::fake('local');
        Http::fake([
            '*' => Http::response('fake-pdf-content', 200, ['Content-Type' => 'application/pdf']),
        ]);

        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => '',
            'NumMedia' => '1',
            'MediaUrl0' => 'https://api.twilio.com/media/test456',
            'MediaContentType0' => 'application/pdf',
        ]);

        $response->assertStatus(200);
        Queue::assertPushed(ExtractBelgeJob::class);
    }

    public function test_xml_media_creates_belge_and_dispatches_job(): void
    {
        Queue::fake();
        Storage::fake('local');
        Http::fake([
            '*' => Http::response('<xml>efatura</xml>', 200, ['Content-Type' => 'text/xml']),
        ]);

        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => '',
            'NumMedia' => '1',
            'MediaUrl0' => 'https://api.twilio.com/media/test789',
            'MediaContentType0' => 'text/xml',
        ]);

        $response->assertStatus(200);
        Queue::assertPushed(ExtractBelgeJob::class);
    }

    public function test_evet_response_updates_konfirmasyon(): void
    {
        $konfirmasyon = Konfirmasyon::factory()->cevapsiz()->create([
            'mukellef_id' => $this->mukellef->id,
        ]);

        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => 'Evet',
            'NumMedia' => '0',
        ]);

        $response->assertStatus(200);
        $response->assertSee('tamamlanmış olarak işaretlendi');

        $konfirmasyon->refresh();
        $this->assertEquals(KonfirmasyonYaniti::Evet, $konfirmasyon->yanit);
        $this->assertNotNull($konfirmasyon->yanit_tarihi);
    }

    public function test_hayir_with_count_updates_konfirmasyon(): void
    {
        $konfirmasyon = Konfirmasyon::factory()->cevapsiz()->create([
            'mukellef_id' => $this->mukellef->id,
        ]);

        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => 'Hayır, 3 tane daha var',
            'NumMedia' => '0',
        ]);

        $response->assertStatus(200);
        $response->assertSee('3 belge daha bekliyoruz');

        $konfirmasyon->refresh();
        $this->assertEquals(KonfirmasyonYaniti::Hayir, $konfirmasyon->yanit);
        $this->assertEquals(3, $konfirmasyon->beklenen_belge_sayisi);
    }

    public function test_hayir_without_count_updates_konfirmasyon(): void
    {
        $konfirmasyon = Konfirmasyon::factory()->cevapsiz()->create([
            'mukellef_id' => $this->mukellef->id,
        ]);

        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => 'Hayır',
            'NumMedia' => '0',
        ]);

        $response->assertStatus(200);
        $response->assertSee('ek belgelerinizi bekliyoruz');

        $konfirmasyon->refresh();
        $this->assertEquals(KonfirmasyonYaniti::Hayir, $konfirmasyon->yanit);
    }

    public function test_text_without_pending_konfirmasyon_returns_info(): void
    {
        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => 'Merhaba',
            'NumMedia' => '0',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Belge göndermek için');
    }

    public function test_multiple_media_creates_multiple_belges(): void
    {
        Queue::fake();
        Storage::fake('local');
        Http::fake([
            '*' => Http::response('content', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $response = $this->post('/api/webhook/whatsapp', [
            'From' => 'whatsapp:+905551234567',
            'Body' => '',
            'NumMedia' => '3',
            'MediaUrl0' => 'https://api.twilio.com/media/a',
            'MediaContentType0' => 'image/jpeg',
            'MediaUrl1' => 'https://api.twilio.com/media/b',
            'MediaContentType1' => 'image/png',
            'MediaUrl2' => 'https://api.twilio.com/media/c',
            'MediaContentType2' => 'application/pdf',
        ]);

        $response->assertStatus(200);
        $response->assertSee('3 belge alındı');
        Queue::assertPushed(ExtractBelgeJob::class, 3);
    }

    public function test_normalize_to_local_works_correctly(): void
    {
        $service = new WhatsAppService;
        $this->assertEquals('05551234567', $service->normalizeToLocal('whatsapp:+905551234567'));
    }
}
