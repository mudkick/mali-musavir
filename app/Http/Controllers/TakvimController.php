<?php

namespace App\Http\Controllers;

use App\Models\BeyannameTakvim;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TakvimController extends Controller
{
    /**
     * Beyanname takvimi - aylık görünüm
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Ay parametresi (YYYY-MM formatında) - yoksa bu ay
        $ay = $request->query('ay');

        if ($ay) {
            try {
                $tarih = Carbon::createFromFormat('Y-m', $ay)->startOfMonth();
            } catch (\Exception $e) {
                $tarih = Carbon::now()->startOfMonth();
            }
        } else {
            $tarih = Carbon::now()->startOfMonth();
        }

        // Ayın başı ve sonu
        $ayBasi = $tarih->copy()->startOfMonth();
        $aySonu = $tarih->copy()->endOfMonth();

        // Kullanıcının mükellef ID'leri
        $mukellefIds = $user->mukellefs()->pluck('id');

        // Takvim kayıtlarını al - günlere göre grupla
        $takvimKayitlari = BeyannameTakvim::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->whereBetween('son_tarih', [$ayBasi, $aySonu])
            ->with(['mukellef', 'beyannameTipi'])
            ->orderBy('son_tarih')
            ->get()
            ->groupBy(fn($item) => $item->son_tarih->format('Y-m-d'));

        return view('takvim.index', compact(
            'takvimKayitlari',
            'tarih',
            'ayBasi',
            'aySonu'
        ));
    }
}
