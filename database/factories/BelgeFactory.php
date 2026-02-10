<?php

namespace Database\Factories;

use App\Enums\BelgeDurumu;
use App\Enums\BelgeTuru;
use App\Models\Mukellef;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Belge>
 */
class BelgeFactory extends Factory
{
    public function definition(): array
    {
        $firmalar = ['ABC Ticaret Ltd.', 'XYZ Elektronik A.Ş.', 'Demir Yapı San.', 'Güneş Gıda Ltd.', 'Yıldız Tekstil A.Ş.'];

        return [
            'mukellef_id' => Mukellef::factory(),
            'gorsel_path' => 'belgeler/'.fake()->uuid().'.jpg',
            'extracted_data' => [
                'vkn' => fake()->numerify('##########'),
                'fatura_no' => fake()->bothify('???#########'),
                'tarih' => fake()->date(),
                'kdv' => fake()->randomFloat(2, 100, 5000),
                'toplam' => fake()->randomFloat(2, 500, 50000),
                'firma' => fake()->randomElement($firmalar),
            ],
            'durum' => fake()->randomElement(BelgeDurumu::cases()),
            'confidence' => fake()->randomFloat(2, 0.6, 1.0),
            'belge_turu' => fake()->randomElement(BelgeTuru::cases()),
        ];
    }

    public function islendi(): static
    {
        return $this->state(fn (array $attributes) => [
            'durum' => BelgeDurumu::Islendi,
            'confidence' => fake()->randomFloat(2, 0.85, 1.0),
        ]);
    }

    public function bekliyor(): static
    {
        return $this->state(fn (array $attributes) => [
            'durum' => BelgeDurumu::Bekliyor,
            'extracted_data' => null,
            'confidence' => null,
        ]);
    }
}
