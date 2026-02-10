@extends('layouts.dashboard')

@section('title', 'Beyanname Takvimi')

@section('content')
    <div x-data="{ selectedDay: null, selectedEntries: [] }">
        <!-- Month Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('takvim.index', ['ay' => $tarih->copy()->subMonth()->format('Y-m')]) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-zinc-300 text-sm font-medium text-zinc-700 rounded-lg hover:bg-zinc-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Önceki Ay
                </a>

                <h2 class="text-xl font-semibold text-slate-800">
                    {{ $tarih->locale('tr')->translatedFormat('F Y') }}
                </h2>

                <a href="{{ route('takvim.index', ['ay' => $tarih->copy()->addMonth()->format('Y-m')]) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-zinc-300 text-sm font-medium text-zinc-700 rounded-lg hover:bg-zinc-50 transition-colors">
                    Sonraki Ay
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <!-- Day Headers -->
            <div class="grid grid-cols-7 gap-px mb-2">
                @foreach(['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'] as $day)
                    <div class="text-center text-xs font-semibold text-zinc-600 uppercase py-2">{{ $day }}</div>
                @endforeach
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 gap-px bg-zinc-200 border border-zinc-200 rounded-lg overflow-hidden">
                @php
                    $daysInMonth = $tarih->daysInMonth;
                    $startDayOfWeek = $tarih->copy()->startOfMonth()->dayOfWeekIso - 1;
                    $today = \Carbon\Carbon::today();
                @endphp

                <!-- Empty cells before first day -->
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="bg-zinc-50 min-h-24"></div>
                @endfor

                <!-- Days of month -->
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $date = $tarih->copy()->day($d);
                        $dateStr = $date->format('Y-m-d');
                        $dayEntries = $takvimKayitlari[$dateStr] ?? collect();
                        $hasEntries = $dayEntries->isNotEmpty();

                        $dotColor = 'bg-zinc-300';
                        if ($hasEntries) {
                            $allVerildi = $dayEntries->every(fn($e) => $e->durum === \App\Enums\BeyannameDurumu::Verildi);
                            $hasUrgent = $dayEntries->contains(function($e) {
                                return $e->durum === \App\Enums\BeyannameDurumu::Bekliyor && $e->son_tarih->isPast();
                            });

                            if ($allVerildi) {
                                $dotColor = 'bg-emerald-500';
                            } elseif ($hasUrgent) {
                                $dotColor = 'bg-rose-500';
                            } else {
                                $dotColor = 'bg-amber-500';
                            }
                        }

                        $isToday = $date->isSameDay($today);
                    @endphp

                    <div @if($hasEntries)
                            @click="selectedDay = {{ $d }}; selectedEntries = {{ $dayEntries->load(['mukellef', 'beyannameTipi'])->toJson() }}"
                         @endif
                         class="bg-white min-h-24 p-2 {{ $hasEntries ? 'cursor-pointer hover:bg-zinc-50' : '' }} {{ $isToday ? 'ring-2 ring-emerald-500 ring-inset' : '' }} transition-colors">
                        <div class="flex items-start justify-between mb-1">
                            <span class="text-sm font-medium {{ $isToday ? 'text-emerald-600' : 'text-zinc-700' }}">{{ $d }}</span>
                            @if($hasEntries)
                                <div class="w-2 h-2 rounded-full {{ $dotColor }}"></div>
                            @endif
                        </div>
                        @if($hasEntries)
                            <div class="text-xs text-zinc-500">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-600 font-medium">
                                    {{ $dayEntries->count() }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endfor

                <!-- Empty cells after last day -->
                @php
                    $totalCells = $startDayOfWeek + $daysInMonth;
                    $remainingCells = (7 - ($totalCells % 7)) % 7;
                @endphp
                @for($i = 0; $i < $remainingCells; $i++)
                    <div class="bg-zinc-50 min-h-24"></div>
                @endfor
            </div>
        </div>

        <!-- Day Detail Panel -->
        <div x-show="selectedDay !== null" x-cloak class="mt-6 bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-slate-800">
                    <span x-text="selectedDay"></span>
                    {{ $tarih->locale('tr')->translatedFormat('F Y') }}
                </h3>
                <button @click="selectedDay = null" class="text-zinc-400 hover:text-zinc-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="entry in selectedEntries" :key="entry.id">
                    <div class="border border-zinc-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-medium text-zinc-800" x-text="entry.mukellef?.ad"></p>
                                <p class="text-sm text-zinc-600 mt-1" x-text="entry.beyanname_tipi?.tip"></p>
                                <p class="text-xs text-zinc-500 mt-1" x-text="entry.donem"></p>
                            </div>
                            <span x-show="entry.durum === 'verildi'"
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                Verildi
                            </span>
                            <span x-show="entry.durum === 'bekliyor'"
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                Bekliyor
                            </span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
@endsection
