@extends('layouts.dashboard')

@section('title', 'Mükellefler')

@section('content')
    <!-- Header with Filter Tabs and Add Button -->
    <div class="mb-6 flex items-center justify-between">
        <!-- Filter Tabs -->
        <div class="flex space-x-2">
        <a href="{{ route('mukellefler.index') }}"
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ !request('filtre') ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
            Tümü
        </a>
        <a href="{{ route('mukellefler.index', ['filtre' => 'eksik']) }}"
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('filtre') === 'eksik' ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
            Eksik Belgesi Olanlar
        </a>
        <a href="{{ route('mukellefler.index', ['filtre' => 'cevapsiz']) }}"
           class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('filtre') === 'cevapsiz' ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
            Yanıt Vermeyenler
        </a>
        </div>

        <!-- Add New Button -->
        <a href="{{ route('mukellefler.create') }}"
           class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Yeni Mükellef Ekle
        </a>
    </div>

    <!-- Mukellefler Table -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200">
        @if($mukellefler->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-zinc-400">Kayıt bulunamadı</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-zinc-50 border-b border-zinc-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Ad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">VKN</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Tür</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Belge Durumu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Yaklaşan Beyanname</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Son Aktivite</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200">
                        @foreach($mukellefler as $mukellef)
                            <tr class="hover:bg-zinc-50 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('mukellefler.show', $mukellef) }}'">
                                <td class="px-6 py-4 text-sm font-medium text-zinc-800">{{ $mukellef->ad }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-600">{{ $mukellef->vkn }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800">
                                        {{ ucfirst($mukellef->tur?->value ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($mukellef->eksik_belge_count > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                            {{ $mukellef->eksik_belge_count }} Eksik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Tamam
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600">
                                    @if($mukellef->beyannameTakvim->first())
                                        {{ ucfirst($mukellef->beyannameTakvim->first()->beyannameTipi?->tip?->value ?? '-') }} - {{ $mukellef->beyannameTakvim->first()->son_tarih->format('d.m.Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600">
                                    {{ $mukellef->updated_at->format('d.m.Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
