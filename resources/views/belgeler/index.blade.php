@extends('layouts.dashboard')

@section('title', 'Gelen Belgeler')

@section('content')
    <div x-data="{
        modalOpen: false,
        selectedBelge: null,
        async showBelge(id) {
            try {
                const response = await fetch(`/belgeler/${id}`);
                const data = await response.json();
                this.selectedBelge = data;
                this.modalOpen = true;
            } catch (error) {
                console.error('Error fetching belge:', error);
            }
        },
        async updateDurum(id, durum) {
            try {
                const response = await fetch(`/belgeler/${id}/durum`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ durum })
                });
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error updating durum:', error);
            }
        }
    }">
        <!-- Filter Tabs -->
        <div class="mb-6 flex space-x-2">
            <a href="{{ route('belgeler.index') }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ !request('filtre') ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
                Tümü
            </a>
            <a href="{{ route('belgeler.index', ['filtre' => 'kontrol_gerekli']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('filtre') === 'kontrol_gerekli' ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
                Kontrol Gerekli
            </a>
            <a href="{{ route('belgeler.index', ['filtre' => 'hata']) }}"
               class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ request('filtre') === 'hata' ? 'bg-zinc-800 text-white' : 'bg-white text-zinc-600 border border-zinc-200 hover:bg-zinc-50' }}">
                Hatalı
            </a>
        </div>

        <!-- Belgeler Table -->
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 mb-6">
            @if($belgeler->isEmpty())
                <div class="px-6 py-12 text-center">
                    <p class="text-zinc-400">Kayıt bulunamadı</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-zinc-50 border-b border-zinc-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Mükellef</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Belge Türü</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Extracted Veri</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Güven</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200">
                            @foreach($belgeler as $belge)
                                <tr class="hover:bg-zinc-50 transition-colors cursor-pointer"
                                    @click="showBelge({{ $belge->id }})">
                                    <td class="px-6 py-4 text-sm font-medium text-zinc-800">{{ $belge->mukellef->ad }}</td>
                                    <td class="px-6 py-4 text-sm text-zinc-600">{{ $belge->created_at->format('d.m.Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-zinc-600">{{ ucfirst($belge->belge_turu?->value ?? '-') }}</td>
                                    <td class="px-6 py-4 text-sm text-zinc-600">
                                        @if($belge->extracted_data)
                                            <div class="space-y-1">
                                                @if(isset($belge->extracted_data['firma_adi']))
                                                    <div class="text-xs">{{ $belge->extracted_data['firma_adi'] }}</div>
                                                @endif
                                                @if(isset($belge->extracted_data['toplam']))
                                                    <div class="text-xs font-medium">{{ number_format($belge->extracted_data['toplam'], 2) }} TL</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-zinc-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
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
                                    <td class="px-6 py-4">
                                        @if($belge->confidence)
                                            <div class="flex items-center space-x-2">
                                                <div class="flex-1 h-2 bg-zinc-200 rounded-full overflow-hidden max-w-[80px]">
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

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-zinc-200">
                    {{ $belgeler->links() }}
                </div>
            @endif
        </div>

        <!-- Modal -->
        <div x-show="modalOpen"
             x-cloak
             @click.self="modalOpen = false"
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div @click.stop
                 class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90">

                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200">
                    <h3 class="text-lg font-semibold text-slate-800">Belge Detayı</h3>
                    <button @click="modalOpen = false" class="text-zinc-400 hover:text-zinc-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6">
                    <template x-if="selectedBelge">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Image Preview -->
                            <div>
                                <h4 class="text-sm font-medium text-zinc-700 mb-3">Görsel</h4>
                                <div class="border border-zinc-200 rounded-lg overflow-hidden bg-zinc-50">
                                    <img x-show="selectedBelge.gorsel_path"
                                         :src="`/storage/${selectedBelge.gorsel_path}`"
                                         alt="Belge görseli"
                                         class="w-full h-auto">
                                    <div x-show="!selectedBelge.gorsel_path" class="p-12 text-center text-zinc-400">
                                        Görsel bulunamadı
                                    </div>
                                </div>
                            </div>

                            <!-- Extracted Data -->
                            <div>
                                <h4 class="text-sm font-medium text-zinc-700 mb-3">Çıkarılan Veriler</h4>
                                <div class="space-y-3">
                                    <template x-if="selectedBelge.extracted_data">
                                        <div>
                                            <template x-for="(value, key) in selectedBelge.extracted_data" :key="key">
                                                <div class="flex items-start py-2 border-b border-zinc-100">
                                                    <span class="text-sm font-medium text-zinc-600 capitalize w-32" x-text="key.replace('_', ' ')"></span>
                                                    <span class="text-sm text-zinc-800" x-text="value"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!selectedBelge.extracted_data">
                                        <p class="text-sm text-zinc-400">Veri bulunamadı</p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end space-x-3 px-6 py-4 border-t border-zinc-200 bg-zinc-50">
                    <button @click="updateDurum(selectedBelge.id, 'hata')"
                            class="px-4 py-2 bg-white border border-rose-300 text-rose-700 text-sm font-medium rounded-lg hover:bg-rose-50 transition-colors">
                        Reddet
                    </button>
                    <button @click="updateDurum(selectedBelge.id, 'islendi')"
                            class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                        Onayla
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
