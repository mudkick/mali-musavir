<?php

namespace App\Jobs;

use App\Models\Belge;
use App\Services\BelgeExtractionService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExtractBelgeJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Belge $belge,
    ) {}

    public function handle(BelgeExtractionService $extractionService, WhatsAppService $whatsAppService): void
    {
        Log::info('Belge extraction başlatıldı', ['belge_id' => $this->belge->id]);

        try {
            // Extract data from image
            $extractedData = $extractionService->extract($this->belge->gorsel_path);

            // Validate the extraction
            $validatedData = $extractionService->validateExtraction($extractedData);

            // Update belge with extracted data
            $this->belge->update([
                'extracted_data' => $validatedData,
                'confidence' => $validatedData['confidence'] ?? 0,
            ]);

            // Determine status based on validation and confidence
            $validationPassed = $validatedData['validation_passed'] ?? false;
            $confidence = $validatedData['confidence'] ?? 0;

            if ($validationPassed && $confidence >= 0.85) {
                $this->belge->update(['durum' => \App\Enums\BelgeDurumu::Islendi]);
                Log::info('Belge başarıyla işlendi', [
                    'belge_id' => $this->belge->id,
                    'confidence' => $confidence,
                ]);
            } else {
                $this->belge->update(['durum' => \App\Enums\BelgeDurumu::KontrolGerekli]);
                Log::warning('Belge kontrol gerektiriyor', [
                    'belge_id' => $this->belge->id,
                    'confidence' => $confidence,
                    'validation_passed' => $validationPassed,
                    'errors' => $validatedData['validation_errors'] ?? [],
                ]);

                // Notify mukellef
                $this->notifyMukellef($whatsAppService, 'kontrol_gerekli', $validatedData);
            }
        } catch (\Exception $e) {
            $this->belge->update(['durum' => \App\Enums\BelgeDurumu::Hata]);

            Log::error('Belge extraction hatası', [
                'belge_id' => $this->belge->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Notify mukellef about error
            $this->notifyMukellef($whatsAppService, 'hata');
        }
    }

    private function notifyMukellef(WhatsAppService $whatsAppService, string $reason, array $data = []): void
    {
        $mukellef = $this->belge->mukellef;

        if (! $mukellef || ! $mukellef->telefon) {
            return;
        }

        $message = match ($reason) {
            'kontrol_gerekli' => sprintf(
                "Merhaba %s,\n\nGönderdiğiniz belge (#%d) işlendi ancak bazı bilgiler kontrol gerektiriyor.\n\nLütfen belgenizin net ve okunaklı olduğundan emin olun. Gerekirse daha iyi kalitede yeni bir fotoğraf gönderebilirsiniz.",
                $mukellef->ad,
                $this->belge->id
            ),
            'hata' => sprintf(
                "Merhaba %s,\n\nGönderdiğiniz belge (#%d) işlenirken bir hata oluştu.\n\nLütfen belgenizi tekrar gönderin veya mali müşavirinizle iletişime geçin.",
                $mukellef->ad,
                $this->belge->id
            ),
            default => 'Belge durumu hakkında bilgilendirme.',
        };

        try {
            $whatsAppService->sendMessage($mukellef->telefon, $message);
        } catch (\Exception $e) {
            Log::error('Mükellefe bildirim gönderilemedi', [
                'mukellef_id' => $mukellef->id,
                'belge_id' => $this->belge->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
