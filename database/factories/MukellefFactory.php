<?php

namespace Database\Factories;

use App\Enums\IletisimKanali;
use App\Enums\MukellefTuru;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mukellef>
 */
class MukellefFactory extends Factory
{
    public function definition(): array
    {
        $tur = fake()->randomElement(MukellefTuru::cases());
        $turkIsimler = ['Ahmet Yılmaz', 'Mehmet Demir', 'Ayşe Kaya', 'Fatma Çelik', 'Ali Öztürk', 'Zeynep Arslan', 'Mustafa Doğan', 'Elif Şahin', 'Hasan Yıldız', 'Hüseyin Aydın'];

        return [
            'user_id' => User::factory(),
            'ad' => fake()->randomElement($turkIsimler),
            'vkn' => fake()->numerify('##########'),
            'tckn' => $tur === MukellefTuru::Sahis ? fake()->numerify('###########') : null,
            'tur' => $tur,
            'telefon' => fake()->numerify('05#########'),
            'email' => fake()->unique()->safeEmail(),
            'iletisim_kanali' => fake()->randomElement(IletisimKanali::cases()),
            'telegram_chat_id' => fake()->numerify('#########'),
            'aktif' => true,
        ];
    }

    public function pasif(): static
    {
        return $this->state(fn (array $attributes) => [
            'aktif' => false,
        ]);
    }

    public function sahis(): static
    {
        return $this->state(fn (array $attributes) => [
            'tur' => MukellefTuru::Sahis,
            'tckn' => fake()->numerify('###########'),
        ]);
    }

    public function limited(): static
    {
        return $this->state(fn (array $attributes) => [
            'tur' => MukellefTuru::Limited,
            'tckn' => null,
        ]);
    }
}
