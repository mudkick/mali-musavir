<?php

namespace Database\Factories;

use App\Enums\KonfirmasyonYaniti;
use App\Models\Mukellef;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Konfirmasyon>
 */
class KonfirmasyonFactory extends Factory
{
    public function definition(): array
    {
        $yanit = fake()->randomElement(KonfirmasyonYaniti::cases());

        return [
            'mukellef_id' => Mukellef::factory(),
            'donem' => sprintf('%d-%02d', now()->year, fake()->numberBetween(1, 12)),
            'mesaj_metni' => 'Sayın mükellemiz, tüm belgelerinizi gönderdiniz mi?',
            'gonderim_tarihi' => fake()->dateTimeBetween('-1 month'),
            'yanit' => $yanit,
            'beklenen_belge_sayisi' => $yanit === KonfirmasyonYaniti::Hayir ? fake()->numberBetween(1, 5) : null,
            'yanit_tarihi' => $yanit !== KonfirmasyonYaniti::Cevapsiz ? fake()->dateTimeBetween('-1 month') : null,
        ];
    }

    public function cevapsiz(): static
    {
        return $this->state(fn (array $attributes) => [
            'yanit' => KonfirmasyonYaniti::Cevapsiz,
            'beklenen_belge_sayisi' => null,
            'yanit_tarihi' => null,
        ]);
    }
}
