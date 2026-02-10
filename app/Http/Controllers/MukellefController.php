<?php

namespace App\Http\Controllers;

use App\Enums\BelgeDurumu;
use App\Enums\BeyannameTipiAdi;
use App\Enums\IletisimKanali;
use App\Enums\KonfirmasyonYaniti;
use App\Enums\MukellefTuru;
use App\Enums\Periyot;
use App\Http\Requests\MukellefStoreRequest;
use App\Http\Requests\MukellefUpdateRequest;
use App\Models\Mukellef;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MukellefController extends Controller
{
    /**
     * Mükellef listesi - filtreleme seçenekleri ile
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Temel sorgu - kullanıcının mükellefleri
        $query = $user->mukellefs()
            ->withCount('belgeler')
            ->withCount([
                'belgeler as eksik_belge_count' => function ($query) {
                    $query->whereIn('durum', [
                        BelgeDurumu::KontrolGerekli,
                        BelgeDurumu::Hata,
                    ]);
                },
            ])
            ->with(['beyannameTakvim' => function ($query) {
                $query->latest('son_tarih')->limit(1);
            }]);

        // Filtre uygula
        $filtre = $request->query('filtre');

        if ($filtre === 'eksik') {
            // Eksik belgesi olan mükellefler
            $query->has('belgeler', '>=', 1)
                ->whereHas('belgeler', function ($q) {
                    $q->whereIn('durum', [
                        BelgeDurumu::KontrolGerekli,
                        BelgeDurumu::Hata,
                    ]);
                });
        } elseif ($filtre === 'cevapsiz') {
            // Cevapsız konfirmasyonu olan mükellefler
            $query->whereHas('konfirmasyonlar', function ($q) {
                $q->where('yanit', KonfirmasyonYaniti::Cevapsiz);
            });
        }

        $mukellefler = $query->get();

        return view('mukellefler.index', compact('mukellefler', 'filtre'));
    }

    /**
     * Mükellef detay sayfası - belgeler, beyannameler, konfirmasyonlar
     */
    public function show(Mukellef $mukellef): View
    {
        // Yetkilendirme - sadece kendi mükellefi görüntüleyebilir
        if ($mukellef->user_id !== auth()->id()) {
            abort(403, 'Bu mükellefi görüntüleme yetkiniz yok.');
        }

        // Eager loading ile ilişkili verileri yükle
        $mukellef->load([
            'belgeler' => function ($query) {
                $query->latest('created_at')->limit(20);
            },
            'beyannameTakvim.beyannameTipi',
            'konfirmasyonlar' => function ($query) {
                $query->latest('gonderim_tarihi')->limit(10);
            },
        ]);

        return view('mukellefler.show', compact('mukellef'));
    }

    /**
     * Yeni mükellef oluşturma formu
     */
    public function create(): View
    {
        $mukellefTurleri = MukellefTuru::cases();
        $iletisimKanallari = IletisimKanali::cases();
        $beyannameTipleri = BeyannameTipiAdi::cases();

        return view('mukellefler.create', compact('mukellefTurleri', 'iletisimKanallari', 'beyannameTipleri'));
    }

    /**
     * Yeni mükellef kaydetme
     */
    public function store(MukellefStoreRequest $request): RedirectResponse
    {
        // Mukellef oluştur - beyanname_tipleri hariç
        $mukellef = auth()->user()->mukellefs()->create(
            $request->safe()->except('beyanname_tipleri')
        );

        // Beyanname tiplerini ata
        if ($request->has('beyanname_tipleri')) {
            foreach ($request->beyanname_tipleri as $tip) {
                $mukellef->beyannameTipleri()->create([
                    'tip' => $tip,
                    'periyot' => $this->getDefaultPeriyot($tip),
                    'son_gun' => $this->getDefaultSonGun($tip),
                ]);
            }
        }

        return redirect()->route('mukellefler.show', $mukellef)->with('success', 'Mükellef başarıyla eklendi.');
    }

    /**
     * Mükellef düzenleme formu
     */
    public function edit(Mukellef $mukellef): View
    {
        if ($mukellef->user_id !== auth()->id()) {
            abort(403);
        }

        $mukellefTurleri = MukellefTuru::cases();
        $iletisimKanallari = IletisimKanali::cases();
        $beyannameTipleri = BeyannameTipiAdi::cases();
        $mevcutBeyannameTipleri = $mukellef->beyannameTipleri()->pluck('tip')->map(fn ($t) => $t->value)->toArray();

        return view('mukellefler.edit', compact('mukellef', 'mukellefTurleri', 'iletisimKanallari', 'beyannameTipleri', 'mevcutBeyannameTipleri'));
    }

    /**
     * Mükellef güncelleme
     */
    public function update(MukellefUpdateRequest $request, Mukellef $mukellef): RedirectResponse
    {
        if ($mukellef->user_id !== auth()->id()) {
            abort(403);
        }

        // Mukellef güncelle - beyanname_tipleri hariç
        $mukellef->update(
            $request->safe()->except('beyanname_tipleri')
        );

        // Beyanname tiplerini güncelle - sync mantığı
        $mevcutTipler = $mukellef->beyannameTipleri()->pluck('tip')->map(fn ($t) => $t->value)->toArray();
        $yeniTipler = $request->beyanname_tipleri ?? [];

        // Kaldırılanları sil
        $mukellef->beyannameTipleri()->whereIn('tip', array_diff($mevcutTipler, $yeniTipler))->delete();

        // Yeni eklenenleri oluştur
        foreach (array_diff($yeniTipler, $mevcutTipler) as $tip) {
            $mukellef->beyannameTipleri()->create([
                'tip' => $tip,
                'periyot' => $this->getDefaultPeriyot($tip),
                'son_gun' => $this->getDefaultSonGun($tip),
            ]);
        }

        return redirect()->route('mukellefler.show', $mukellef)->with('success', 'Mükellef başarıyla güncellendi.');
    }

    /**
     * Mükellef silme (soft delete)
     */
    public function destroy(Mukellef $mukellef): RedirectResponse
    {
        if ($mukellef->user_id !== auth()->id()) {
            abort(403);
        }

        $mukellef->delete(); // soft delete

        return redirect()->route('mukellefler.index')->with('success', 'Mükellef başarıyla silindi.');
    }

    /**
     * Beyanname tipi için varsayılan periyot değerini döndürür
     */
    private function getDefaultPeriyot(string $tip): string
    {
        return match ($tip) {
            'kdv', 'muhtasar' => Periyot::Aylik->value,
            'gecici_vergi' => Periyot::Ucaylik->value,
            'ba_bs' => Periyot::Aylik->value,
            'yillik_gelir', 'yillik_kurumlar' => Periyot::Yillik->value,
            default => Periyot::Aylik->value,
        };
    }

    /**
     * Beyanname tipi için varsayılan son gün değerini döndürür
     */
    private function getDefaultSonGun(string $tip): int
    {
        return match ($tip) {
            'kdv' => 26,
            'muhtasar' => 26,
            'gecici_vergi' => 17,
            'ba_bs' => 5,
            'yillik_gelir' => 31,
            'yillik_kurumlar' => 30,
            default => 26,
        };
    }
}
