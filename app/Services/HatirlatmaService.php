<?php

namespace App\Services;

use App\Enums\BeyannameDurumu;
use App\Enums\BildirimDurumu;
use App\Enums\SablonTuru;
use App\Models\BeyannameTakvim;
use App\Models\Bildirim;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class HatirlatmaService
{
    /**
     * Yaklaşan son tarihleri kontrol eder.
     * 5, 3, 1 gün kala beyannameleri bulur.
     *
     * @return Collection<int, BeyannameTakvim>
     */
    public function checkUpcomingDeadlines(): Collection
    {
        $today = Carbon::today();
        $reminderDays = [5, 3, 1];

        $dates = array_map(fn ($days) => $today->copy()->addDays($days), $reminderDays);

        return BeyannameTakvim::query()
            ->whereIn('son_tarih', $dates)
            ->whereIn('durum', [BeyannameDurumu::Bekliyor, BeyannameDurumu::Hazirlaniyor])
            ->with(['mukellef.user', 'beyannameTipi'])
            ->get();
    }

    /**
     * Yaklaşan beyannameler için hatırlatma bildirimleri gönderir.
     */
    public function sendReminders(): int
    {
        $yaklasanlar = $this->checkUpcomingDeadlines();
        $count = 0;

        foreach ($yaklasanlar as $takvim) {
            $kalanGun = Carbon::today()->diffInDays($takvim->son_tarih);
            $tipAdi = $takvim->beyannameTipi?->tip?->value ?? 'Beyanname';

            $mesaj = "Sayın {$takvim->mukellef->ad}, {$takvim->donem} dönemi ".
                     ucfirst(str_replace('_', ' ', $tipAdi)).
                     " beyannamesi son tarihine {$kalanGun} gün kaldı. Son tarih: {$takvim->son_tarih->format('d.m.Y')}";

            // Mükellefe bildirim
            Bildirim::create([
                'mukellef_id' => $takvim->mukellef_id,
                'sablon_turu' => SablonTuru::OdemeHatirlatma,
                'mesaj_metni' => $mesaj,
                'kanal' => $takvim->mukellef->iletisim_kanali?->value,
                'durum' => BildirimDurumu::Gonderildi,
                'gonderim_tarihi' => now(),
            ]);

            $count++;
        }

        Log::info("Hatırlatma: {$count} hatırlatma bildirimi gönderildi.");

        return $count;
    }
}
