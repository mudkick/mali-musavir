<?php

namespace Database\Factories;

use App\Enums\BeyannameTipiAdi;
use App\Enums\Periyot;
use App\Models\Mukellef;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeyannameTipi>
 */
class BeyannameTipiFactory extends Factory
{
    /** @var array<string, array{periyot: Periyot, son_gun: int}> */
    private static array $tipAyarlari = [
        'kdv' => ['periyot' => Periyot::Aylik, 'son_gun' => 26],
        'muhtasar' => ['periyot' => Periyot::Aylik, 'son_gun' => 26],
        'gecici_vergi' => ['periyot' => Periyot::Ucaylik, 'son_gun' => 17],
        'ba_bs' => ['periyot' => Periyot::Aylik, 'son_gun' => 5],
        'yillik_gelir' => ['periyot' => Periyot::Yillik, 'son_gun' => 25],
        'yillik_kurumlar' => ['periyot' => Periyot::Yillik, 'son_gun' => 25],
    ];

    public function definition(): array
    {
        $tip = fake()->randomElement(BeyannameTipiAdi::cases());
        $ayar = self::$tipAyarlari[$tip->value];

        return [
            'mukellef_id' => Mukellef::factory(),
            'tip' => $tip,
            'periyot' => $ayar['periyot'],
            'son_gun' => $ayar['son_gun'],
        ];
    }
}
