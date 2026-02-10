<?php

namespace Database\Seeders;

use App\Enums\BeyannameDurumu;
use App\Enums\BeyannameTipiAdi;
use App\Enums\Periyot;
use App\Models\Belge;
use App\Models\BeyannameTakvim;
use App\Models\BeyannameTipi;
use App\Models\Bildirim;
use App\Models\Konfirmasyon;
use App\Models\Mukellef;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /** @var array<string, array{periyot: Periyot, son_gun: int}> */
    private array $beyannameAyarlari = [
        'kdv' => ['periyot' => Periyot::Aylik, 'son_gun' => 26],
        'muhtasar' => ['periyot' => Periyot::Aylik, 'son_gun' => 26],
        'gecici_vergi' => ['periyot' => Periyot::Ucaylik, 'son_gun' => 17],
        'ba_bs' => ['periyot' => Periyot::Aylik, 'son_gun' => 5],
    ];

    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Mali Müşavir',
            'email' => 'muhasebe@example.com',
        ]);

        $mukellefler = Mukellef::factory(5)->create([
            'user_id' => $user->id,
        ]);

        foreach ($mukellefler as $mukellef) {
            Belge::factory(fake()->numberBetween(10, 15))->create([
                'mukellef_id' => $mukellef->id,
            ]);

            $tipler = [];
            foreach ($this->beyannameAyarlari as $tipAdi => $ayar) {
                $tipler[] = BeyannameTipi::factory()->create([
                    'mukellef_id' => $mukellef->id,
                    'tip' => BeyannameTipiAdi::from($tipAdi),
                    'periyot' => $ayar['periyot'],
                    'son_gun' => $ayar['son_gun'],
                ]);
            }

            foreach ($tipler as $tip) {
                $aylar = $tip->periyot === Periyot::Ucaylik ? [1, 4, 7, 10] : range(1, 12);

                foreach (array_slice($aylar, 0, 3) as $ay) {
                    BeyannameTakvim::factory()->create([
                        'mukellef_id' => $mukellef->id,
                        'beyanname_tipi_id' => $tip->id,
                        'donem' => sprintf('%d-%02d', now()->year, $ay),
                        'son_tarih' => now()->setYear(now()->year)->setMonth($ay)->setDay($tip->son_gun),
                        'durum' => fake()->randomElement(BeyannameDurumu::cases()),
                    ]);
                }
            }

            Konfirmasyon::factory(2)->create([
                'mukellef_id' => $mukellef->id,
            ]);

            Bildirim::factory(3)->create([
                'mukellef_id' => $mukellef->id,
            ]);
        }
    }
}
