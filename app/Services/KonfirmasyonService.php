<?php

namespace App\Services;

use App\Enums\BeyannameDurumu;
use App\Enums\BildirimDurumu;
use App\Enums\KonfirmasyonYaniti;
use App\Enums\SablonTuru;
use App\Models\BeyannameTakvim;
use App\Models\Bildirim;
use App\Models\Konfirmasyon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KonfirmasyonService
{
    /**
     * Beyanname tarihinden 7 gün önce mükelleflere konfirmasyon mesajı gönderir.
     */
    public function sendConfirmations(): int
    {
        $targetDate = Carbon::today()->addDays(7);

        $yaklasanlar = BeyannameTakvim::query()
            ->whereDate('son_tarih', $targetDate)
            ->whereIn('durum', [BeyannameDurumu::Bekliyor, BeyannameDurumu::Hazirlaniyor])
            ->with(['mukellef', 'beyannameTipi'])
            ->get();

        $count = 0;

        foreach ($yaklasanlar as $takvim) {
            // Bu dönem için zaten konfirmasyon gönderilmiş mi?
            $exists = Konfirmasyon::where('mukellef_id', $takvim->mukellef_id)
                ->where('donem', $takvim->donem)
                ->exists();

            if ($exists) {
                continue;
            }

            $mesaj = "Sayın {$takvim->mukellef->ad}, {$takvim->donem} dönemi için tüm belgelerinizi gönderdiniz mi?";

            Konfirmasyon::create([
                'mukellef_id' => $takvim->mukellef_id,
                'donem' => $takvim->donem,
                'mesaj_metni' => $mesaj,
                'gonderim_tarihi' => now(),
                'yanit' => KonfirmasyonYaniti::Cevapsiz,
            ]);

            $count++;
        }

        Log::info("Konfirmasyon: {$count} konfirmasyon mesajı gönderildi.");

        return $count;
    }

    /**
     * 2 gün geçip yanıt gelmeyenlere tekrar gönderir.
     */
    public function checkUnanswered(): int
    {
        $cevapsizlar = Konfirmasyon::where('yanit', KonfirmasyonYaniti::Cevapsiz)
            ->where('gonderim_tarihi', '<=', now()->subDays(2))
            ->with('mukellef')
            ->get();

        $count = 0;

        foreach ($cevapsizlar as $konfirmasyon) {
            // Tekrar bildirim gönder
            Bildirim::create([
                'mukellef_id' => $konfirmasyon->mukellef_id,
                'sablon_turu' => SablonTuru::EksikBelge,
                'mesaj_metni' => "Hatırlatma: {$konfirmasyon->donem} dönemi için belge konfirmasyonunuza yanıt bekliyoruz.",
                'kanal' => $konfirmasyon->mukellef->iletisim_kanali?->value,
                'durum' => BildirimDurumu::Gonderildi,
                'gonderim_tarihi' => now(),
            ]);

            $count++;
        }

        Log::info("Konfirmasyon: {$count} cevapsız hatırlatma gönderildi.");

        return $count;
    }

    /**
     * Cevapsız konfirmasyonlar için muhasebeciye uyarı bildirim gönderir.
     */
    public function notifyAccountant(): void
    {
        $cevapsizlar = Konfirmasyon::where('yanit', KonfirmasyonYaniti::Cevapsiz)
            ->where('gonderim_tarihi', '<=', now()->subDays(2))
            ->with('mukellef.user')
            ->get()
            ->groupBy(fn ($k) => $k->mukellef->user_id);

        foreach ($cevapsizlar as $userId => $group) {
            $mukellefAdlari = $group->pluck('mukellef.ad')->unique()->implode(', ');

            Log::warning("Muhasebeci #{$userId} için cevapsız konfirmasyonlar: {$mukellefAdlari}");
            // TODO: WhatsApp bildirimi gönder (Faz II)
        }
    }
}
