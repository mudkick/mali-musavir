<?php

namespace App\Services;

use App\Enums\BeyannameDurumu;
use App\Enums\Periyot;
use App\Models\BeyannameTakvim;
use App\Models\BeyannameTipi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BeyannameTakvimService
{
    /**
     * Verilen dönem için tüm mükelleflerin beyanname takvim kayıtlarını oluşturur.
     * Dönem formatı: YYYY-MM (örn: 2025-03)
     */
    public function generateMonthlyCalendar(?string $donem = null): int
    {
        $donem = $donem ?? Carbon::now()->addMonth()->format('Y-m');
        $donemCarbon = Carbon::createFromFormat('Y-m', $donem);

        $count = 0;

        // Tüm aktif beyanname tiplerini al
        $beyannameTipleri = BeyannameTipi::query()
            ->whereHas('mukellef', fn ($q) => $q->where('aktif', true))
            ->with('mukellef')
            ->get();

        foreach ($beyannameTipleri as $tip) {
            // Bu dönem için bu tip uygun mu kontrol et
            if (! $this->shouldGenerateForPeriod($tip, $donemCarbon)) {
                continue;
            }

            // Zaten var mı kontrol et
            $exists = BeyannameTakvim::where('mukellef_id', $tip->mukellef_id)
                ->where('beyanname_tipi_id', $tip->id)
                ->where('donem', $donem)
                ->exists();

            if ($exists) {
                continue;
            }

            // Son tarih hesapla
            $sonTarih = $this->calculateDeadline($tip, $donemCarbon);

            BeyannameTakvim::create([
                'mukellef_id' => $tip->mukellef_id,
                'beyanname_tipi_id' => $tip->id,
                'donem' => $donem,
                'son_tarih' => $sonTarih,
                'durum' => BeyannameDurumu::Bekliyor,
            ]);

            $count++;
        }

        Log::info("BeyannameTakvim: {$donem} dönemi için {$count} kayıt oluşturuldu.");

        return $count;
    }

    /**
     * Bu beyanname tipinin verilen dönem için oluşturulması gerekip gerekmediğini kontrol eder.
     */
    private function shouldGenerateForPeriod(BeyannameTipi $tip, Carbon $donem): bool
    {
        return match ($tip->periyot) {
            Periyot::Aylik => true,
            Periyot::Ucaylik => in_array($donem->month, [1, 4, 7, 10]),
            Periyot::Yillik => $donem->month === 3, // Mart ayında yıllık beyannameler
            default => true,
        };
    }

    /**
     * Beyanname son tarihini hesaplar.
     */
    private function calculateDeadline(BeyannameTipi $tip, Carbon $donem): Carbon
    {
        // Sonraki ayın son_gun'ünde teslim
        $deadline = $donem->copy()->addMonth()->day(min($tip->son_gun, $donem->copy()->addMonth()->daysInMonth));

        // Hafta sonuna denk gelirse Pazartesiye kaydır
        if ($deadline->isWeekend()) {
            $deadline = $deadline->nextWeekday();
        }

        return $deadline;
    }
}
