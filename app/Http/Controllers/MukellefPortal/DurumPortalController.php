<?php

namespace App\Http\Controllers\MukellefPortal;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DurumPortalController extends Controller
{
    /**
     * Mükellefin beyanname takvimi ve durum özeti.
     */
    public function index(): View
    {
        $mukellef = auth('mukellef')->user();

        $beyannameler = $mukellef->beyannameTakvim()
            ->with('beyannameTipi')
            ->latest('son_tarih')
            ->paginate(20);

        $buAyBelgeSayisi = $mukellef->belgeler()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('mukellef-portal.durum', compact('beyannameler', 'buAyBelgeSayisi'));
    }
}
