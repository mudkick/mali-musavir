<?php

use App\Services\BeyannameTakvimService;
use App\Services\GunlukOzetService;
use App\Services\HatirlatmaService;
use App\Services\KonfirmasyonService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Her gün 08:00 - Günlük özet
Schedule::call(function () {
    app(GunlukOzetService::class)->sendDailySummaries();
})->dailyAt('08:00')->name('gunluk-ozet');

// Her gün 09:00 - Hatırlatmalar
Schedule::call(function () {
    app(HatirlatmaService::class)->sendReminders();
})->dailyAt('09:00')->name('hatirlatmalar');

// Her gün 09:00 - Konfirmasyonlar
Schedule::call(function () {
    $service = app(KonfirmasyonService::class);
    $service->sendConfirmations();
    $service->checkUnanswered();
    $service->notifyAccountant();
})->dailyAt('09:00')->name('konfirmasyonlar');

// Her ayın 1'i - Takvim oluşturma
Schedule::call(function () {
    app(BeyannameTakvimService::class)->generateMonthlyCalendar();
})->monthlyOn(1, '06:00')->name('takvim-olustur');
