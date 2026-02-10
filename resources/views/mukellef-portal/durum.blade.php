@extends('mukellef-portal.layouts.app')

@section('title', 'Durum')

@section('content')
<div class="space-y-6">
    <!-- Özet Kartı -->
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-sm p-6 text-white">
        <h2 class="text-base font-semibold mb-4">Bu Ay</h2>
        <div class="flex items-end space-x-2">
            <span class="text-4xl font-bold">{{ $buAyBelgeSayisi }}</span>
            <span class="text-emerald-100 mb-1">belge gönderildi</span>
        </div>
    </div>

    <!-- Beyanname Durumları -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200">
        <div class="px-6 py-4 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-slate-800">Beyanname Durumları</h2>
        </div>

        @if($beyannameler->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 text-zinc-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-zinc-500 text-sm">Beyanname kaydı yok</p>
            </div>
        @else
            <div class="divide-y divide-zinc-200">
                @foreach($beyannameler as $beyanname)
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-slate-800 mb-1">
                                    {{ ucfirst(str_replace('_', ' ', $beyanname->beyannameTipi?->tip?->value ?? 'Beyanname')) }}
                                </h3>
                                <p class="text-xs text-zinc-500">{{ $beyanname->donem }}</p>
                            </div>
                            <div>
                                @if($beyanname->durum === App\Enums\BeyannameDurumu::Verildi)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Verildi
                                    </span>
                                @elseif($beyanname->durum === App\Enums\BeyannameDurumu::Hazir)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Hazır
                                    </span>
                                @elseif($beyanname->durum === App\Enums\BeyannameDurumu::Hazirlaniyor)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Hazırlanıyor
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Bekliyor
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center space-x-4 text-xs text-zinc-600">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Son Tarih: {{ $beyanname->son_tarih->format('d.m.Y') }}</span>
                            </div>

                            @if($beyanname->son_tarih->isPast())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                    Gecikmiş
                                </span>
                            @elseif($beyanname->son_tarih->diffInDays(now()) <= 3)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Yaklaşıyor
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($beyannameler->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200">
                    {{ $beyannameler->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
