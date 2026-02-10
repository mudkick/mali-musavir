@extends('mukellef-portal.layouts.app')

@section('title', 'Giriş')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-14rem)]">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <!-- Icon & Title -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800">Giriş Yap</h2>
                <p class="text-sm text-zinc-600 mt-1">Telefon numaranız ile giriş yapın</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('mukellef-portal.send-code') }}">
                @csrf

                <div class="mb-6">
                    <label for="telefon" class="block text-sm font-medium text-zinc-700 mb-2">
                        Telefon Numarası <span class="text-rose-600">*</span>
                    </label>
                    <input type="tel"
                           id="telefon"
                           name="telefon"
                           value="{{ old('telefon') }}"
                           required
                           class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                           placeholder="+90 5XX XXX XXXX">
                    @error('telefon')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Doğrulama Kodu Gönder
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
