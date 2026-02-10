<?php

namespace Tests\Feature;

use App\Enums\BeyannameDurumu;
use App\Enums\KonfirmasyonYaniti;
use App\Models\BeyannameTakvim;
use App\Models\BeyannameTipi;
use App\Models\Bildirim;
use App\Models\Konfirmasyon;
use App\Models\Mukellef;
use App\Models\User;
use App\Services\KonfirmasyonService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KonfirmasyonServiceTest extends TestCase
{
    use RefreshDatabase;

    private KonfirmasyonService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new KonfirmasyonService;
    }

    public function test_sends_confirmations_7_days_before_deadline(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        $tip = BeyannameTipi::factory()->create(['mukellef_id' => $mukellef->id]);

        BeyannameTakvim::factory()->create([
            'mukellef_id' => $mukellef->id,
            'beyanname_tipi_id' => $tip->id,
            'donem' => '2025-03',
            'son_tarih' => Carbon::today()->addDays(7),
            'durum' => BeyannameDurumu::Bekliyor,
        ]);

        $count = $this->service->sendConfirmations();

        $this->assertEquals(1, $count);
        $this->assertEquals(1, Konfirmasyon::count());
    }

    public function test_does_not_duplicate_confirmations(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);
        $tip = BeyannameTipi::factory()->create(['mukellef_id' => $mukellef->id]);

        BeyannameTakvim::factory()->create([
            'mukellef_id' => $mukellef->id,
            'beyanname_tipi_id' => $tip->id,
            'donem' => '2025-03',
            'son_tarih' => Carbon::today()->addDays(7),
            'durum' => BeyannameDurumu::Bekliyor,
        ]);

        $this->service->sendConfirmations();
        $count = $this->service->sendConfirmations();

        $this->assertEquals(0, $count);
    }

    public function test_check_unanswered_sends_reminders_after_2_days(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);

        Konfirmasyon::factory()->create([
            'mukellef_id' => $mukellef->id,
            'yanit' => KonfirmasyonYaniti::Cevapsiz,
            'gonderim_tarihi' => now()->subDays(3),
        ]);

        $count = $this->service->checkUnanswered();

        $this->assertEquals(1, $count);
        $this->assertEquals(1, Bildirim::count());
    }

    public function test_does_not_remind_answered_confirmations(): void
    {
        $user = User::factory()->create();
        $mukellef = Mukellef::factory()->create(['user_id' => $user->id, 'aktif' => true]);

        Konfirmasyon::factory()->create([
            'mukellef_id' => $mukellef->id,
            'yanit' => KonfirmasyonYaniti::Evet,
            'gonderim_tarihi' => now()->subDays(3),
        ]);

        $count = $this->service->checkUnanswered();

        $this->assertEquals(0, $count);
    }
}
