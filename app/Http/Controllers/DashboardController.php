<?php

namespace App\Http\Controllers;

use App\Enums\BeyannameDurumu;
use App\Enums\BelgeDurumu;
use App\Enums\KonfirmasyonYaniti;
use App\Models\Belge;
use App\Models\BeyannameTakvim;
use App\Models\Konfirmasyon;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard ana sayfası - özet istatistikler ve son belgeler
     */
    public function index(): View
    {
        $user = auth()->user();

        // Kullanıcının tüm mükellef ID'lerini al
        $mukellefIds = $user->mukellefs()->pluck('id');

        // Yaklaşan beyannameler (7 gün içinde, henüz verilmemiş)
        $yaklasanBeyanname = BeyannameTakvim::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->whereBetween('son_tarih', [Carbon::today(), Carbon::today()->addDays(7)])
            ->where('durum', '!=', BeyannameDurumu::Verildi)
            ->count();

        // Eksik belge olan mükellef sayısı (kontrol gerekli veya hata durumunda)
        $eksikBelgeMukellef = Belge::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->whereIn('durum', [BelgeDurumu::KontrolGerekli, BelgeDurumu::Hata])
            ->distinct('mukellef_id')
            ->count('mukellef_id');

        // Bu hafta yüklenen belge sayısı
        $buHaftaBelge = Belge::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->where('created_at', '>=', Carbon::now()->startOfWeek())
            ->count();

        // Yanıt bekleyen konfirmasyon sayısı
        $yanitBekleyen = Konfirmasyon::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->where('yanit', KonfirmasyonYaniti::Cevapsiz)
            ->count();

        // Son yüklenen belgeler (en son 10 adet)
        $sonBelgeler = Belge::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->with('mukellef')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'yaklasanBeyanname',
            'eksikBelgeMukellef',
            'buHaftaBelge',
            'yanitBekleyen',
            'sonBelgeler'
        ));
    }
}
