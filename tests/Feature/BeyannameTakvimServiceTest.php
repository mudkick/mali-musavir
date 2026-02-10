<?php

namespace Tests\Feature;

use App\Enums\BeyannameDurumu;
use App\Enums\BeyannameTipiAdi;
use App\Enums\Periyot;
use App\Models\BeyannameTakvim;
use App\Models\BeyannameTipi;
use App\Models\Mukellef;
use App\Models\User;
use App\Services\BeyannameTakvimService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeyannameTakvimServiceTest extends TestCase
{
    use RefreshDatabase;

    private BeyannameTakvimService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BeyannameTakvimService;
    }

    public function test_generates_monthly_calendar_for_active_mukellefs(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        BeyannameTipi::factory()->create([
            'mukellef_id' => $mukellef->id,
            'tip' => BeyannameTipiAdi::Kdv,
            'periyot' => Periyot::Aylik,
            'son_gun' => 26,
        ]);

        $count = $this->service->generateMonthlyCalendar('2025-03');

        $this->assertEquals(1, $count);
        $this->assertDatabaseHas('beyanname_takvim', [
            'mukellef_id' => $mukellef->id,
            'donem' => '2025-03',
            'durum' => BeyannameDurumu::Bekliyor->value,
        ]);
    }

    public function test_does_not_duplicate_existing_entries(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        BeyannameTipi::factory()->create([
            'mukellef_id' => $mukellef->id,
            'tip' => BeyannameTipiAdi::Kdv,
            'periyot' => Periyot::Aylik,
            'son_gun' => 26,
        ]);

        $this->service->generateMonthlyCalendar('2025-03');
        $count = $this->service->generateMonthlyCalendar('2025-03');

        $this->assertEquals(0, $count);
        $this->assertEquals(1, BeyannameTakvim::where('donem', '2025-03')->count());
    }

    public function test_skips_inactive_mukellefs(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => false]);
        BeyannameTipi::factory()->create([
            'mukellef_id' => $mukellef->id,
            'tip' => BeyannameTipiAdi::Kdv,
            'periyot' => Periyot::Aylik,
            'son_gun' => 26,
        ]);

        $count = $this->service->generateMonthlyCalendar('2025-03');

        $this->assertEquals(0, $count);
    }

    public function test_quarterly_type_only_generates_for_correct_months(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        BeyannameTipi::factory()->create([
            'mukellef_id' => $mukellef->id,
            'tip' => BeyannameTipiAdi::GeciciVergi,
            'periyot' => Periyot::Ucaylik,
            'son_gun' => 17,
        ]);

        // Ocak = çeyrek ay, oluşturulmalı
        $count1 = $this->service->generateMonthlyCalendar('2025-01');
        $this->assertEquals(1, $count1);

        // Şubat = çeyrek ay değil, atlanmalı
        $count2 = $this->service->generateMonthlyCalendar('2025-02');
        $this->assertEquals(0, $count2);
    }

    public function test_yearly_type_generates_only_in_march(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        BeyannameTipi::factory()->create([
            'mukellef_id' => $mukellef->id,
            'tip' => BeyannameTipiAdi::YillikGelir,
            'periyot' => Periyot::Yillik,
            'son_gun' => 31,
        ]);

        $countMarch = $this->service->generateMonthlyCalendar('2025-03');
        $this->assertEquals(1, $countMarch);

        $countApril = $this->service->generateMonthlyCalendar('2025-04');
        $this->assertEquals(0, $countApril);
    }
}
