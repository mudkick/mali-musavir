<?php

namespace Database\Factories;

use App\Enums\BildirimDurumu;
use App\Enums\IletisimKanali;
use App\Enums\SablonTuru;
use App\Models\Mukellef;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bildirim>
 */
class BildirimFactory extends Factory
{
    public function definition(): array
    {
        $sablonMesajlari = [
            SablonTuru::EksikBelge->value => 'Sayın mükellemiz, eksik belgeleriniz bulunmaktadır.',
            SablonTuru::BeyannameOnay->value => 'Beyannameniz hazırlanmıştır, onayınızı bekliyoruz.',
            SablonTuru::OdemeHatirlatma->value => 'Vergi ödeme süreniz yaklaşmaktadır.',
            SablonTuru::Genel->value => 'Mali müşavirinizden bilgilendirme mesajı.',
        ];

        $sablon = fake()->randomElement(SablonTuru::cases());

        return [
            'mukellef_id' => Mukellef::factory(),
            'sablon_turu' => $sablon,
            'mesaj_metni' => $sablonMesajlari[$sablon->value],
            'kanal' => fake()->randomElement(IletisimKanali::cases()),
            'durum' => fake()->randomElement(BildirimDurumu::cases()),
            'gonderim_tarihi' => fake()->dateTimeBetween('-1 month'),
        ];
    }
}
