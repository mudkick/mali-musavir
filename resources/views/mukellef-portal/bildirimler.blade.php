@extends('mukellef-portal.layouts.app')

@section('title', 'Bildirimler')

@section('content')
<div x-data="{
    async sendKonfirmasyonYanit(konfirmasyonId, yanit, belgeSayisi = null) {
        try {
            const response = await fetch('{{ route("mukellef-portal.konfirmasyon-yanit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    konfirmasyon_id: konfirmasyonId,
                    yanit: yanit,
                    beklenen_belge_sayisi: belgeSayisi
                })
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Yanıt gönderilemedi. Lütfen tekrar deneyin.');
            }
        } catch (e) {
            alert('Bağlantı hatası.');
        }
    }
}">
    <!-- Bekleyen Konfirmasyonlar -->
    @if($konfirmasyonlar->isNotEmpty())
        <div class="mb-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-800">Onay Bekleyen</h2>

            @foreach($konfirmasyonlar as $konfirmasyon)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6"
                     x-data="{ showInput: false, belgeSayisi: '' }">
                    <div class="mb-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-sm font-medium text-amber-800">{{ $konfirmasyon->donem }}</span>
                        </div>
                        <p class="text-sm text-zinc-700">{{ $konfirmasyon->mesaj_metni }}</p>
                    </div>

                    <div class="space-y-2">
                        <button @click="sendKonfirmasyonYanit({{ $konfirmasyon->id }}, 'evet')"
                                class="w-full py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                            Evet, tamamdır
                        </button>

                        <div>
                            <button @click="showInput = !showInput"
                                    class="w-full py-3 bg-white border border-rose-300 text-rose-700 text-sm font-medium rounded-lg hover:bg-rose-50 transition-colors">
                                Hayır
                            </button>

                            <div x-show="showInput" x-cloak class="mt-3 space-y-2">
                                <label for="belge-sayisi-{{ $konfirmasyon->id }}" class="block text-sm font-medium text-zinc-700">
                                    Kaç belge daha göndereceksiniz?
                                </label>
                                <input type="number"
                                       id="belge-sayisi-{{ $konfirmasyon->id }}"
                                       x-model="belgeSayisi"
                                       min="1"
                                       class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                                       placeholder="0">
                                <button @click="sendKonfirmasyonYanit({{ $konfirmasyon->id }}, 'hayir', belgeSayisi)"
                                        class="w-full py-3 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                    Gönder
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Tüm Bildirimler -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200">
        <div class="px-6 py-4 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-slate-800">Mesajlar</h2>
        </div>

        @if($bildirimler->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 text-zinc-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-zinc-500 text-sm">Henüz bildirim yok</p>
            </div>
        @else
            <div class="divide-y divide-zinc-200">
                @foreach($bildirimler as $bildirim)
                    <div class="px-6 py-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if($bildirim->sablon_turu === App\Enums\SablonTuru::OdemeHatirlatma)
                                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @elseif($bildirim->sablon_turu === App\Enums\SablonTuru::EksikBelge)
                                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-zinc-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-xs font-medium text-emerald-600">
                                        {{ ucfirst(str_replace('_', ' ', $bildirim->sablon_turu?->value ?? 'Bildirim')) }}
                                    </span>
                                    <span class="text-xs text-zinc-400">•</span>
                                    <span class="text-xs text-zinc-500">{{ $bildirim->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-zinc-700 whitespace-pre-line">{{ $bildirim->mesaj_metni }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bildirimler->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200">
                    {{ $bildirimler->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
