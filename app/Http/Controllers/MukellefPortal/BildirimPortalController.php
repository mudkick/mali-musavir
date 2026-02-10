<?php

namespace App\Http\Controllers\MukellefPortal;

use App\Enums\KonfirmasyonYaniti;
use App\Http\Controllers\Controller;
use App\Models\Konfirmasyon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BildirimPortalController extends Controller
{
    /**
     * Mükellefin bildirimlerini ve konfirmasyonlarını listele.
     */
    public function index(): View
    {
        $mukellef = auth('mukellef')->user();
        $bildirimler = $mukellef->bildirimler()->latest('gonderim_tarihi')->paginate(20);
        $konfirmasyonlar = $mukellef->konfirmasyonlar()
            ->where('yanit', KonfirmasyonYaniti::Cevapsiz)
            ->latest('gonderim_tarihi')
            ->get();

        return view('mukellef-portal.bildirimler', compact('bildirimler', 'konfirmasyonlar'));
    }

    /**
     * Konfirmasyona yanıt ver.
     */
    public function konfirmasyonYanit(Request $request): JsonResponse
    {
        $request->validate([
            'konfirmasyon_id' => ['required', 'integer', 'exists:konfirmasyonlar,id'],
            'yanit' => ['required', 'in:evet,hayir'],
            'beklenen_belge_sayisi' => ['nullable', 'integer', 'min:1'],
        ]);

        $mukellef = auth('mukellef')->user();
        $konfirmasyon = Konfirmasyon::where('id', $request->konfirmasyon_id)
            ->where('mukellef_id', $mukellef->id)
            ->firstOrFail();

        $konfirmasyon->update([
            'yanit' => KonfirmasyonYaniti::from($request->yanit),
            'yanit_tarihi' => now(),
            'beklenen_belge_sayisi' => $request->yanit === 'hayir' ? $request->beklenen_belge_sayisi : null,
        ]);

        return response()->json(['message' => 'Yanıtınız kaydedildi.']);
    }
}
