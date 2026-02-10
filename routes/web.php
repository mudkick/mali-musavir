<?php

use App\Http\Controllers\BelgeController;
use App\Http\Controllers\BildirimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MukellefController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TakvimController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mükellefler
    Route::resource('mukellefler', MukellefController::class)->parameters(['mukellefler' => 'mukellef']);

    // Beyanname Takvimi
    Route::get('/takvim', [TakvimController::class, 'index'])->name('takvim.index');

    // Belgeler
    Route::get('/belgeler', [BelgeController::class, 'index'])->name('belgeler.index');
    Route::get('/belgeler/{belge}', [BelgeController::class, 'show'])->name('belgeler.show');
    Route::patch('/belgeler/{belge}/durum', [BelgeController::class, 'updateDurum'])->name('belgeler.durum');

    // Bildirimler
    Route::get('/bildirimler', [BildirimController::class, 'index'])->name('bildirimler.index');
    Route::post('/bildirimler', [BildirimController::class, 'store'])->name('bildirimler.store');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Mükellef Portal - Auth
Route::prefix('mukellef-portal')->name('mukellef-portal.')->group(function () {
    Route::get('/login', [App\Http\Controllers\MukellefPortal\MukellefAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\MukellefPortal\MukellefAuthController::class, 'sendCode'])->name('send-code');
    Route::get('/verify', [App\Http\Controllers\MukellefPortal\MukellefAuthController::class, 'showVerify'])->name('verify');
    Route::post('/verify', [App\Http\Controllers\MukellefPortal\MukellefAuthController::class, 'verifyCode'])->name('verify-code');

    // Protected routes
    Route::middleware('mukellef.auth')->group(function () {
        Route::post('/logout', [App\Http\Controllers\MukellefPortal\MukellefAuthController::class, 'logout'])->name('logout');
        Route::get('/belgeler', [App\Http\Controllers\MukellefPortal\BelgePortalController::class, 'index'])->name('belgeler');
        Route::post('/belge-yukle', [App\Http\Controllers\MukellefPortal\BelgePortalController::class, 'upload'])->name('belge-yukle');
        Route::get('/bildirimler', [App\Http\Controllers\MukellefPortal\BildirimPortalController::class, 'index'])->name('bildirimler');
        Route::post('/konfirmasyon-yanit', [App\Http\Controllers\MukellefPortal\BildirimPortalController::class, 'konfirmasyonYanit'])->name('konfirmasyon-yanit');
        Route::get('/durum', [App\Http\Controllers\MukellefPortal\DurumPortalController::class, 'index'])->name('durum');
    });

    Route::get('/offline', fn () => view('mukellef-portal.offline'))->name('offline');
});

require __DIR__.'/auth.php';
