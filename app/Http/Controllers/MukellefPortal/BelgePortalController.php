<?php

namespace App\Http\Controllers\MukellefPortal;

use App\Enums\BelgeDurumu;
use App\Enums\BelgeTuru;
use App\Http\Controllers\Controller;
use App\Jobs\ExtractBelgeJob;
use App\Models\Belge;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BelgePortalController extends Controller
{
    /**
     * Mükellefin belgelerini listele.
     */
    public function index(): View
    {
        $mukellef = auth('mukellef')->user();
        $belgeler = $mukellef->belgeler()->latest()->paginate(20);

        return view('mukellef-portal.belgeler', compact('belgeler'));
    }

    /**
     * Yeni belge yükle.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'dosya' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,xml', 'max:10240'],
        ]);

        $mukellef = auth('mukellef')->user();
        $dosya = $request->file('dosya');
        $path = $dosya->store("belgeler/{$mukellef->id}", 'public');

        $belge = Belge::create([
            'mukellef_id' => $mukellef->id,
            'gorsel_path' => $path,
            'belge_turu' => BelgeTuru::Fatura,
            'durum' => BelgeDurumu::Bekliyor,
        ]);

        ExtractBelgeJob::dispatch($belge);

        return response()->json([
            'message' => 'Belgeniz alındı, işleniyor...',
            'belge' => $belge,
        ], 201);
    }
}
