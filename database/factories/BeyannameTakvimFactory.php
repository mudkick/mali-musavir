<?php

namespace Database\Factories;

use App\Enums\BeyannameDurumu;
use App\Models\BeyannameTipi;
use App\Models\Mukellef;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BeyannameTakvim>
 */
class BeyannameTakvimFactory extends Factory
{
    public function definition(): array
    {
        $yil = now()->year;
        $ay = fake()->numberBetween(1, 12);
        $donem = sprintf('%d-%02d', $yil, $ay);

        return [
            'mukellef_id' => Mukellef::factory(),
            'beyanname_tipi_id' => BeyannameTipi::factory(),
            'donem' => $donem,
            'son_tarih' => now()->setYear($yil)->setMonth($ay)->endOfMonth(),
            'durum' => fake()->randomElement(BeyannameDurumu::cases()),
        ];
    }
}
