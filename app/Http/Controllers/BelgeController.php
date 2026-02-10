<?php

namespace App\Http\Controllers;

use App\Enums\BelgeDurumu;
use App\Models\Belge;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class BelgeController extends Controller
{
    /**
     * Belge listesi - sayfalama ve filtreleme ile
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Kullanıcının mükellef ID'leri
        $mukellefIds = $user->mukellefs()->pluck('id');

        // Temel sorgu
        $query = Belge::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->with('mukellef')
            ->latest('created_at');

        // Filtre uygula
        $filtre = $request->query('filtre');

        if ($filtre === 'kontrol_gerekli') {
            $query->where('durum', BelgeDurumu::KontrolGerekli);
        } elseif ($filtre === 'hata') {
            $query->where('durum', BelgeDurumu::Hata);
        }

        $belgeler = $query->paginate(20);

        return view('belgeler.index', compact('belgeler', 'filtre'));
    }

    /**
     * Belge detayı - modal için JSON response
     */
    public function show(Belge $belge): JsonResponse
    {
        // Yetkilendirme - belgenin mükellefinin sahibi olmalı
        if ($belge->mukellef->user_id !== auth()->id()) {
            abort(403, 'Bu belgeyi görüntüleme yetkiniz yok.');
        }

        // Mükellef ilişkisini yükle
        $belge->load('mukellef');

        return response()->json($belge);
    }

    /**
     * Belge durumunu güncelle
     */
    public function updateDurum(Request $request, Belge $belge): JsonResponse
    {
        // Yetkilendirme
        if ($belge->mukellef->user_id !== auth()->id()) {
            abort(403, 'Bu belgeyi düzenleme yetkiniz yok.');
        }

        // Validasyon
        $validated = $request->validate([
            'durum' => ['required', 'in:islendi,hata'],
        ]);

        // Durumu güncelle
        $belge->update([
            'durum' => BelgeDurumu::from($validated['durum']),
        ]);

        return response()->json(['message' => 'Belge durumu başarıyla güncellendi.']);
    }
}
