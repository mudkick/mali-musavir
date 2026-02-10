@extends('layouts.dashboard')

@section('title', $mukellef->ad)

@section('content')
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800 mb-2">{{ $mukellef->ad }}</h1>
                <div class="flex items-center space-x-4 text-sm text-zinc-600">
                    <span>VKN: {{ $mukellef->vkn }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800">
                        {{ ucfirst($mukellef->tur?->value ?? '-') }}
                    </span>
                </div>
                <div class="mt-3 flex items-center space-x-4 text-sm text-zinc-600">
                    @if($mukellef->telefon)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $mukellef->telefon }}
                        </span>
                    @endif
                    @if($mukellef->email)
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $mukellef->email }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('mukellefler.edit', $mukellef) }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-zinc-300 text-zinc-700 text-sm font-medium rounded-lg hover:bg-zinc-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Düzenle
                </a>
                <form method="POST" action="{{ route('mukellefler.destroy', $mukellef) }}"
                      onsubmit="return confirm('Bu mükellefi silmek istediğinizden emin misiniz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-lg hover:bg-rose-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Sil
                    </button>
                </form>
                <a href="{{ route('bildirimler.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Hatırlatma Gönder
                </a>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: 'belgeler' }" class="bg-white rounded-xl shadow-sm border border-zinc-200">
        <!-- Tab Headers -->
        <div class="border-b border-zinc-200">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'belgeler'"
                        :class="activeTab === 'belgeler' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300'"
                        class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                    Belgeler
                </button>
                <button @click="activeTab = 'takvim'"
                        :class="activeTab === 'takvim' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300'"
                        class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                    Beyanname Takvimi
                </button>
                <button @click="activeTab = 'konfirmasyon'"
                        :class="activeTab === 'konfirmasyon' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300'"
                        class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                    Konfirmasyonlar
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Belgeler Tab -->
            <div x-show="activeTab === 'belgeler'" x-cloak>
                @if($mukellef->belgeler->isEmpty())
                    <p class="text-center text-zinc-400 py-8">Kayıt bulunamadı</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 border-b border-zinc-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Tarih</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Tür</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Durum</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Güven</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                @foreach($mukellef->belgeler as $belge)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ $belge->created_at->format('d.m.Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ ucfirst($belge->belge_turu?->value ?? '-') }}</td>
                                        <td class="px-4 py-3">
                                            @if($belge->durum === App\Enums\BelgeDurumu::Islendi)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">İşlendi</span>
                                            @elseif($belge->durum === App\Enums\BelgeDurumu::Bekliyor)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Bekliyor</span>
                                            @elseif($belge->durum === App\Enums\BelgeDurumu::KontrolGerekli)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Kontrol</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Hata</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($belge->confidence)
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex-1 h-2 bg-zinc-200 rounded-full overflow-hidden">
                                                        <div class="h-full rounded-full {{ $belge->confidence >= 0.85 ? 'bg-emerald-500' : ($belge->confidence >= 0.6 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                             style="width: {{ $belge->confidence * 100 }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-zinc-600">{{ round($belge->confidence * 100) }}%</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-zinc-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Beyanname Takvimi Tab -->
            <div x-show="activeTab === 'takvim'" x-cloak>
                @if($mukellef->beyannameTakvim->isEmpty())
                    <p class="text-center text-zinc-400 py-8">Kayıt bulunamadı</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 border-b border-zinc-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Dönem</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Beyanname Tipi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Son Tarih</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Durum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                @foreach($mukellef->beyannameTakvim as $takvim)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ $takvim->donem }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ ucfirst($takvim->beyannameTipi?->tip?->value ?? '-') }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ $takvim->son_tarih->format('d.m.Y') }}</td>
                                        <td class="px-4 py-3">
                                            @if($takvim->durum === App\Enums\BeyannameDurumu::Verildi)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Verildi</span>
                                            @elseif($takvim->durum === App\Enums\BeyannameDurumu::Hazir)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Hazır</span>
                                            @elseif($takvim->durum === App\Enums\BeyannameDurumu::Hazirlaniyor)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Hazırlanıyor</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Bekliyor</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Konfirmasyonlar Tab -->
            <div x-show="activeTab === 'konfirmasyon'" x-cloak>
                @if($mukellef->konfirmasyonlar->isEmpty())
                    <p class="text-center text-zinc-400 py-8">Kayıt bulunamadı</p>
                @else
                    <div class="space-y-4">
                        @foreach($mukellef->konfirmasyonlar as $konfirmasyon)
                            <div class="border border-zinc-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p class="text-sm font-medium text-zinc-800">{{ $konfirmasyon->donem }}</p>
                                        <p class="text-xs text-zinc-500 mt-1">Gönderim: {{ $konfirmasyon->gonderim_tarihi?->format('d.m.Y H:i') ?? '-' }}</p>
                                    </div>
                                    @if($konfirmasyon->yanit === App\Enums\KonfirmasyonYaniti::Evet)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Evet</span>
                                    @elseif($konfirmasyon->yanit === App\Enums\KonfirmasyonYaniti::Hayir)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Hayır</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Cevapsız</span>
                                    @endif
                                </div>
                                <p class="text-sm text-zinc-600">{{ $konfirmasyon->mesaj_metni }}</p>
                                @if($konfirmasyon->beklenen_belge_sayisi)
                                    <p class="text-xs text-zinc-500 mt-2">Beklenen belge: {{ $konfirmasyon->beklenen_belge_sayisi }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
