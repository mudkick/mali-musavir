<?php

namespace App\Http\Controllers\Api;

use App\Enums\BelgeDurumu;
use App\Enums\BelgeTuru;
use App\Enums\KonfirmasyonYaniti;
use App\Http\Controllers\Controller;
use App\Jobs\ExtractBelgeJob;
use App\Models\Belge;
use App\Models\Konfirmasyon;
use App\Models\Mukellef;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsApp,
    ) {}

    public function __invoke(Request $request): Response
    {
        $from = $request->input('From', '');
        $body = trim($request->input('Body', ''));
        $numMedia = (int) $request->input('NumMedia', 0);

        $telefon = $this->whatsApp->normalizeToLocal($from);
        $mukellef = Mukellef::query()->where('telefon', $telefon)->first();

        if (! $mukellef) {
            return $this->twimlResponse('Tanımlanamayan numara, lütfen muhasebecinize başvurun.');
        }

        if ($numMedia > 0) {
            return $this->handleMedia($request, $mukellef, $numMedia);
        }

        return $this->handleText($mukellef, $body);
    }

    private function handleMedia(Request $request, Mukellef $mukellef, int $numMedia): Response
    {
        $processed = 0;

        for ($i = 0; $i < $numMedia; $i++) {
            $mediaUrl = $request->input("MediaUrl{$i}");
            $contentType = $request->input("MediaContentType{$i}", '');

            if (! $mediaUrl) {
                continue;
            }

            $filePath = $this->whatsApp->downloadMedia($mediaUrl);

            $belgeTuru = match (true) {
                str_contains($contentType, 'image/') => BelgeTuru::Fatura,
                str_contains($contentType, 'application/pdf') => BelgeTuru::Fatura,
                str_contains($contentType, 'text/xml'),
                str_contains($contentType, 'application/xml') => BelgeTuru::Fatura,
                default => BelgeTuru::Diger,
            };

            $belge = Belge::query()->create([
                'mukellef_id' => $mukellef->id,
                'gorsel_path' => $filePath,
                'durum' => BelgeDurumu::Bekliyor,
                'belge_turu' => $belgeTuru,
            ]);

            ExtractBelgeJob::dispatch($belge);
            $processed++;
        }

        return $this->twimlResponse("Teşekkürler! {$processed} belge alındı ve işleniyor.");
    }

    private function handleText(Mukellef $mukellef, string $body): Response
    {
        $pendingKonfirmasyon = Konfirmasyon::query()
            ->where('mukellef_id', $mukellef->id)
            ->where('yanit', KonfirmasyonYaniti::Cevapsiz)
            ->latest('gonderim_tarihi')
            ->first();

        if (! $pendingKonfirmasyon) {
            return $this->twimlResponse('Mesajınız alındı. Belge göndermek için fotoğraf veya PDF yükleyebilirsiniz.');
        }

        $lowerBody = mb_strtolower($body);

        if (in_array($lowerBody, ['evet', 'e', 'tamam'])) {
            $pendingKonfirmasyon->update([
                'yanit' => KonfirmasyonYaniti::Evet,
                'yanit_tarihi' => now(),
            ]);

            return $this->twimlResponse('Teşekkürler! Belgeleriniz tamamlanmış olarak işaretlendi.');
        }

        if (preg_match('/hay[ıi]r.*?(\d+)/iu', $body, $matches)) {
            $pendingKonfirmasyon->update([
                'yanit' => KonfirmasyonYaniti::Hayir,
                'beklenen_belge_sayisi' => (int) $matches[1],
                'yanit_tarihi' => now(),
            ]);

            return $this->twimlResponse("Anlaşıldı, {$matches[1]} belge daha bekliyoruz. Lütfen fotoğraf veya PDF olarak gönderin.");
        }

        if (in_array($lowerBody, ['hayır', 'hayir', 'h'])) {
            $pendingKonfirmasyon->update([
                'yanit' => KonfirmasyonYaniti::Hayir,
                'yanit_tarihi' => now(),
            ]);

            return $this->twimlResponse('Anlaşıldı, ek belgelerinizi bekliyoruz. Lütfen fotoğraf veya PDF olarak gönderin.');
        }

        return $this->twimlResponse('Mesajınız alındı. "Evet" veya "Hayır, X tane daha var" şeklinde yanıt verebilirsiniz.');
    }

    private function twimlResponse(string $message): Response
    {
        $twiml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<Response><Message>{$message}</Message></Response>";

        return response($twiml, 200, ['Content-Type' => 'text/xml']);
    }
}
