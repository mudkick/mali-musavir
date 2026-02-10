<?php

namespace App\Services;

use App\Enums\BelgeDurumu;
use App\Enums\BeyannameDurumu;
use App\Models\Belge;
use App\Models\BeyannameTakvim;
use App\Models\Mukellef;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GunlukOzetService
{
    /**
     * Her muhasebeciye günlük özet gönderir.
     */
    public function sendDailySummaries(): void
    {
        $users = User::has('mukellefler')->get();

        foreach ($users as $user) {
            $summary = $this->generateSummary($user);

            Log::info("Günlük Özet - {$user->name}:", $summary);
            // TODO: WhatsApp bildirimi gönder (Faz II)
        }
    }

    /**
     * Muhasebeci için günlük özet verileri oluşturur.
     *
     * @return array{yaklasan_beyanname: int, eksik_belge_mukellef: int, dun_gelen_belge: int}
     */
    public function generateSummary(User $user): array
    {
        $mukellefIds = $user->mukellefler()->pluck('id');
        $today = Carbon::today();

        // Bugün yaklaşan beyanname sayısı (3 gün içinde)
        $yaklasanBeyanname = BeyannameTakvim::whereIn('mukellef_id', $mukellefIds)
            ->whereBetween('son_tarih', [$today, $today->copy()->addDays(3)])
            ->whereIn('durum', [BeyannameDurumu::Bekliyor, BeyannameDurumu::Hazirlaniyor])
            ->count();

        // Eksik belgesi olan mükellef sayısı
        $eksikBelgeMukellef = Mukellef::whereIn('id', $mukellefIds)
            ->where('aktif', true)
            ->whereHas('belgeler', fn ($q) => $q->whereIn('durum', [BelgeDurumu::KontrolGerekli, BelgeDurumu::Hata]))
            ->count();

        // Dün gelen belge sayısı
        $dunGelenBelge = Belge::whereIn('mukellef_id', $mukellefIds)
            ->whereDate('created_at', $today->copy()->subDay())
            ->count();

        return [
            'yaklasan_beyanname' => $yaklasanBeyanname,
            'eksik_belge_mukellef' => $eksikBelgeMukellef,
            'dun_gelen_belge' => $dunGelenBelge,
        ];
    }
}
