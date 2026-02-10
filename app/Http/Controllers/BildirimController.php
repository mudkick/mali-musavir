<?php

namespace App\Http\Controllers;

use App\Enums\BelgeDurumu;
use App\Enums\BildirimDurumu;
use App\Enums\SablonTuru;
use App\Models\Bildirim;
use App\Models\Mukellef;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BildirimController extends Controller
{
    /**
     * Bildirim listesi ve gönderim formu
     */
    public function index(): View
    {
        $user = auth()->user();

        // Kullanıcının mükellef ID'leri
        $mukellefIds = $user->mukellefs()->pluck('id');

        // Bildirimleri sayfalama ile al
        $bildirimler = Bildirim::query()
            ->whereIn('mukellef_id', $mukellefIds)
            ->with('mukellef')
            ->latest('gonderim_tarihi')
            ->paginate(20);

        // Form için gerekli veriler
        $mukellefler = $user->mukellefs()
            ->where('aktif', true)
            ->withCount([
                'belgeler as eksik_belge_count' => function ($query) {
                    $query->whereIn('durum', [BelgeDurumu::KontrolGerekli, BelgeDurumu::Hata]);
                },
            ])
            ->get();
        $sablonlar = SablonTuru::cases();

        return view('bildirimler.index', compact(
            'bildirimler',
            'mukellefler',
            'sablonlar'
        ));
    }

    /**
     * Toplu bildirim gönderimi
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Validasyon
        $validated = $request->validate([
            'mukellef_ids' => ['required', 'array', 'min:1'],
            'mukellef_ids.*' => [
                'required',
                'integer',
                Rule::exists('mukellefs', 'id')->where('user_id', $user->id),
            ],
            'sablon_turu' => ['required', 'string', Rule::in(array_map(fn($case) => $case->value, SablonTuru::cases()))],
            'mesaj_metni' => ['required', 'string', 'max:1000'],
        ]);

        // Her mükellef için bildirim oluştur
        $bildirimler = [];
        foreach ($validated['mukellef_ids'] as $mukellefId) {
            $mukellef = Mukellef::find($mukellefId);

            $bildirimler[] = [
                'mukellef_id' => $mukellefId,
                'sablon_turu' => $validated['sablon_turu'],
                'mesaj_metni' => $validated['mesaj_metni'],
                'kanal' => $mukellef->iletisim_kanali?->value,
                'durum' => BildirimDurumu::Gonderildi->value,
                'gonderim_tarihi' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        // Toplu insert
        Bildirim::insert($bildirimler);

        $mukellefSayisi = count($validated['mukellef_ids']);

        return redirect()->back()->with('success', "{$mukellefSayisi} mükellefeye bildirim başarıyla gönderildi.");
    }
}
