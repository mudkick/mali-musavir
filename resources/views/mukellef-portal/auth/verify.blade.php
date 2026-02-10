@extends('mukellef-portal.layouts.app')

@section('title', 'Doğrulama')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-14rem)]">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <!-- Icon & Title -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800">Doğrulama Kodu</h2>
                <p class="text-sm text-zinc-600 mt-2">
                    <span class="font-medium">{{ session('telefon') }}</span> numarasına gönderilen 6 haneli kodu girin
                </p>
            </div>

            <!-- Verification Form -->
            <form method="POST" action="{{ route('mukellef-portal.verify-code') }}">
                @csrf

                <div class="mb-6">
                    <label for="code" class="block text-sm font-medium text-zinc-700 mb-2">
                        Doğrulama Kodu <span class="text-rose-600">*</span>
                    </label>
                    <input type="text"
                           id="code"
                           name="code"
                           value="{{ old('code') }}"
                           required
                           maxlength="6"
                           class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500 text-center text-2xl tracking-widest"
                           placeholder="000000">
                    @error('code')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors mb-3">
                    Doğrula
                </button>

                <div class="text-center">
                    <a href="{{ route('mukellef-portal.login') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                        Farklı numara ile giriş
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
