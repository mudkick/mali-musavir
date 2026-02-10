<?php

namespace App\Http\Controllers\MukellefPortal;

use App\Http\Controllers\Controller;
use App\Models\Mukellef;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MukellefAuthController extends Controller
{
    /**
     * Login formunu göster.
     */
    public function showLogin(): View
    {
        return view('mukellef-portal.auth.login');
    }

    /**
     * Telefon numarasına SMS doğrulama kodu gönder.
     */
    public function sendCode(Request $request): RedirectResponse
    {
        $request->validate([
            'telefon' => ['required', 'string'],
        ]);

        $mukellef = Mukellef::where('telefon', $request->telefon)
            ->where('aktif', true)
            ->first();

        if (! $mukellef) {
            return back()->withErrors(['telefon' => 'Bu telefon numarası ile kayıtlı mükellef bulunamadı.']);
        }

        // Şimdilik .env'den sabit kod oku
        $code = config('services.mukellef_portal.sms_code', '123456');

        // Kodu session'a kaydet (5 dakika geçerli)
        session([
            'mukellef_auth_code' => $code,
            'mukellef_auth_telefon' => $request->telefon,
            'mukellef_auth_expires' => now()->addMinutes(5),
        ]);

        return redirect()->route('mukellef-portal.verify')->with('telefon', $request->telefon);
    }

    /**
     * SMS doğrulama ekranını göster.
     */
    public function showVerify(): View
    {
        return view('mukellef-portal.auth.verify');
    }

    /**
     * SMS doğrulama kodunu kontrol et ve giriş yap.
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $storedCode = session('mukellef_auth_code');
        $telefon = session('mukellef_auth_telefon');
        $expires = session('mukellef_auth_expires');

        if (! $storedCode || ! $telefon || now()->isAfter($expires)) {
            return redirect()->route('mukellef-portal.login')
                ->withErrors(['code' => 'Doğrulama kodunun süresi dolmuş.']);
        }

        if ($request->code !== $storedCode) {
            return back()->withErrors(['code' => 'Geçersiz doğrulama kodu.']);
        }

        $mukellef = Mukellef::where('telefon', $telefon)
            ->where('aktif', true)
            ->first();

        if (! $mukellef) {
            return redirect()->route('mukellef-portal.login')
                ->withErrors(['telefon' => 'Mükellef bulunamadı.']);
        }

        auth('mukellef')->login($mukellef);

        session()->forget(['mukellef_auth_code', 'mukellef_auth_telefon', 'mukellef_auth_expires']);

        return redirect()->route('mukellef-portal.belgeler');
    }

    /**
     * Mükellef çıkış yapar.
     */
    public function logout(): RedirectResponse
    {
        auth('mukellef')->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('mukellef-portal.login');
    }
}
