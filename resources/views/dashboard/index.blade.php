@extends('layouts.dashboard')

@section('title', 'Ana Sayfa')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Yaklaşan Beyanname -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-2">Yaklaşan Beyanname</p>
                    <p class="text-3xl font-semibold text-slate-800">{{ $yaklasanBeyanname }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Eksik Belge -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-2">Eksik Belge</p>
                    <p class="text-3xl font-semibold text-slate-800">{{ $eksikBelgeMukellef }}</p>
                </div>
                <div class="w-12 h-12 bg-rose-50 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Bu Hafta Gelen -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-2">Bu Hafta Gelen</p>
                    <p class="text-3xl font-semibold text-slate-800">{{ $buHaftaBelge }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Yanıt Bekleyen -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-2">Yanıt Bekleyen</p>
                    <p class="text-3xl font-semibold text-slate-800">{{ $yanitBekleyen }}</p>
                </div>
                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Gelen Belgeler -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 mb-6">
        <div class="px-6 py-4 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-slate-800">Son Gelen Belgeler</h2>
        </div>

        @if($sonBelgeler->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-zinc-400">Kayıt bulunamadı</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 border-b border-zinc-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Mükellef Adı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Tarih</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Belge Türü</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @foreach($sonBelgeler as $belge)
                            <tr class="hover:bg-zinc-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-zinc-600">{{ $belge->mukellef->ad }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-600">{{ $belge->created_at->format('d.m.Y') }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-600">{{ ucfirst($belge->belge_turu?->value ?? '-') }}</td>
                                <td class="px-6 py-4">
                                    @if($belge->durum === App\Enums\BelgeDurumu::Islendi)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            İşlendi
                                        </span>
                                    @elseif($belge->durum === App\Enums\BelgeDurumu::Bekliyor)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Bekliyor
                                        </span>
                                    @elseif($belge->durum === App\Enums\BelgeDurumu::KontrolGerekli)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Kontrol Gerekli
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                            Hata
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Hızlı Aksiyonlar -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Hızlı Aksiyonlar</h3>
        <a href="{{ route('bildirimler.index') }}"
           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            Toplu Hatırlatma Gönder
        </a>
    </div>
@endsection
