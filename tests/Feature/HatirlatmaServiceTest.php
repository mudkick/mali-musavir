<?php

namespace Tests\Feature;

use App\Enums\BeyannameDurumu;
use App\Models\BeyannameTakvim;
use App\Models\BeyannameTipi;
use App\Models\Bildirim;
use App\Models\Mukellef;
use App\Models\User;
use App\Services\HatirlatmaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HatirlatmaServiceTest extends TestCase
{
    use RefreshDatabase;

    private HatirlatmaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HatirlatmaService;
    }

    public function test_finds_deadlines_5_3_1_days_away(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        $tip = BeyannameTipi::factory()->create(['mukellef_id' => $mukellef->id]);

        // 3 gün sonra deadline
        BeyannameTakvim::factory()->create([
            'mukellef_id' => $mukellef->id,
            'beyanname_tipi_id' => $tip->id,
            'son_tarih' => Carbon::today()->addDays(3),
            'durum' => BeyannameDurumu::Bekliyor,
        ]);

        // 10 gün sonra deadline (bulunmamalı)
        BeyannameTakvim::factory()->create([
            'mukellef_id' => $mukellef->id,
            'beyanname_tipi_id' => $tip->id,
            'son_tarih' => Carbon::today()->addDays(10),
            'durum' => BeyannameDurumu::Bekliyor,
        ]);

        $results = $this->service->checkUpcomingDeadlines();

        $this->assertCount(1, $results);
    }

    public function test_send_reminders_creates_bildirim_records(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        $tip = BeyannameTipi::factory()->create(['mukellef_id' => $mukellef->id]);

        BeyannameTakvim::factory()->create([
            'mukellef_id' => $mukellef->id,
            'beyanname_tipi_id' => $tip->id,
            'son_tarih' => Carbon::today()->addDays(1),
            'durum' => BeyannameDurumu::Bekliyor,
        ]);

        $count = $this->service->sendReminders();

        $this->assertEquals(1, $count);
        $this->assertEquals(1, Bildirim::count());
    }

    public function test_skips_already_submitted_declarations(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        $tip = BeyannameTipi::factory()->create(['mukellef_id' => $mukellef->id]);

        BeyannameTakvim::factory()->create([
            'mukellef_id' => $mukellef->id,
            'beyanname_tipi_id' => $tip->id,
            'son_tarih' => Carbon::today()->addDays(3),
            'durum' => BeyannameDurumu::Verildi,
        ]);

        $results = $this->service->checkUpcomingDeadlines();

        $this->assertCount(0, $results);
    }
}
