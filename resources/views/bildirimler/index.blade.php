@extends('layouts.dashboard')

@section('title', 'Bildirimler')

@section('content')
    <div x-data="{
        selectedMukellefler: [],
        selectedSablon: '',
        mesaj: '',
        sablonlar: {
            'eksik_belge': 'Sayın {mukellef}, eksik belgelerinizi en kısa sürede göndermenizi rica ederiz.',
            'beyanname_onay': 'Sayın {mukellef}, beyannameniz hazırlanmıştır. Onayınızı bekliyoruz.',
            'odeme_hatirlatma': 'Sayın {mukellef}, vergi ödeme süreniz yaklaşmaktadır.',
            'genel': ''
        },
        selectEksikBelge() {
            const checkboxes = document.querySelectorAll('input[name=mukellef_ids\\[\\]]:not(:checked)');
            checkboxes.forEach(cb => {
                if (cb.dataset.eksikBelge === 'true') {
                    cb.checked = true;
                    if (!this.selectedMukellefler.includes(parseInt(cb.value))) {
                        this.selectedMukellefler.push(parseInt(cb.value));
                    }
                }
            });
        },
        updateMesaj() {
            this.mesaj = this.sablonlar[this.selectedSablon] || '';
        }
    }">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Yeni Bildirim Gönder -->
            <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-6">Yeni Bildirim Gönder</h2>

                <form method="POST" action="{{ route('bildirimler.store') }}">
                    @csrf

                    <!-- Mükellef Seçimi -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-zinc-700 mb-2">Mükellef Seçimi</label>

                        <button type="button"
                                @click="selectEksikBelge"
                                class="mb-3 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                            Eksik belgesi olanları seç
                        </button>

                        <div class="border border-zinc-300 rounded-lg max-h-48 overflow-y-auto">
                            @foreach($mukellefler as $mukellef)
                                <label class="flex items-center px-3 py-2 hover:bg-zinc-50 cursor-pointer border-b border-zinc-100 last:border-b-0">
                                    <input type="checkbox"
                                           name="mukellef_ids[]"
                                           value="{{ $mukellef->id }}"
                                           data-eksik-belge="{{ $mukellef->eksik_belge_count > 0 ? 'true' : 'false' }}"
                                           class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500"
                                           x-model="selectedMukellefler">
                                    <span class="ml-3 text-sm text-zinc-700">
                                        {{ $mukellef->ad }}
                                        @if($mukellef->eksik_belge_count > 0)
                                            <span class="ml-2 text-xs text-rose-600">({{ $mukellef->eksik_belge_count }} eksik)</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        @error('mukellefler')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Şablon Seçimi -->
                    <div class="mb-6">
                        <label for="sablon_turu" class="block text-sm font-medium text-zinc-700 mb-2">Şablon</label>
                        <select id="sablon_turu"
                                name="sablon_turu"
                                x-model="selectedSablon"
                                @change="updateMesaj"
                                class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Şablon seçin...</option>
                            @foreach($sablonlar as $sablon)
                                <option value="{{ $sablon->value }}">{{ ucfirst(str_replace('_', ' ', $sablon->value)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mesaj Metni -->
                    <div class="mb-6">
                        <label for="mesaj_metni" class="block text-sm font-medium text-zinc-700 mb-2">Mesaj Metni</label>
                        <textarea id="mesaj_metni"
                                  name="mesaj_metni"
                                  rows="6"
                                  x-model="mesaj"
                                  class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                                  placeholder="Mesajınızı yazın..."></textarea>
                        @error('mesaj_metni')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                        Gönder
                    </button>
                </form>
            </div>

            <!-- Gönderilmiş Bildirimler -->
            <div class="bg-white rounded-xl shadow-sm border border-zinc-200">
                <div class="px-6 py-4 border-b border-zinc-200">
                    <h2 class="text-lg font-semibold text-slate-800">Gönderilmiş Bildirimler</h2>
                </div>

                @if($bildirimler->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <p class="text-zinc-400">Kayıt bulunamadı</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-zinc-50 border-b border-zinc-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Tarih</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Mükellef</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Şablon</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-zinc-600 uppercase tracking-wider">Durum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200">
                                @foreach($bildirimler as $bildirim)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ $bildirim->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ $bildirim->mukellef->ad }}</td>
                                        <td class="px-4 py-3 text-sm text-zinc-600">{{ $bildirim->sablon_turu ? ucfirst(str_replace('_', ' ', $bildirim->sablon_turu->value)) : 'Genel' }}</td>
                                        <td class="px-4 py-3">
                                            @if($bildirim->durum === App\Enums\BildirimDurumu::Gonderildi)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Gönderildi</span>
                                            @elseif($bildirim->durum === App\Enums\BildirimDurumu::Bekliyor)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Bekliyor</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Hata</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-zinc-200">
                        {{ $bildirimler->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
