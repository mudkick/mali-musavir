@extends('mukellef-portal.layouts.app')

@section('title', 'Belgeler')

@section('content')
<div x-data="{
    uploading: false,
    uploadSuccess: false,
    uploadError: '',
    async uploadFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.uploading = true;
        this.uploadSuccess = false;
        this.uploadError = '';

        const formData = new FormData();
        formData.append('dosya', file);

        try {
            const response = await fetch('{{ route('mukellef-portal.belge-yukle') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                body: formData
            });

            if (response.ok) {
                this.uploadSuccess = true;
                setTimeout(() => window.location.reload(), 2000);
            } else {
                const data = await response.json();
                this.uploadError = data.message || 'Yükleme başarısız.';
            }
        } catch (e) {
            this.uploadError = 'Bağlantı hatası.';
        } finally {
            this.uploading = false;
        }
    }
}">
    <!-- Upload Card -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Belge Yükle</h2>

        <div class="grid grid-cols-2 gap-3 mb-4">
            <!-- Photo Button -->
            <label class="flex flex-col items-center justify-center py-4 px-3 bg-emerald-50 border-2 border-dashed border-emerald-300 rounded-lg cursor-pointer hover:bg-emerald-100 transition-colors">
                <input type="file"
                       accept="image/*"
                       capture="environment"
                       @change="uploadFile"
                       class="hidden">
                <svg class="w-8 h-8 text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-medium text-emerald-700">Fotoğraf Çek</span>
            </label>

            <!-- File Button -->
            <label class="flex flex-col items-center justify-center py-4 px-3 bg-emerald-50 border-2 border-dashed border-emerald-300 rounded-lg cursor-pointer hover:bg-emerald-100 transition-colors">
                <input type="file"
                       accept="image/*,.pdf,.xml"
                       @change="uploadFile"
                       class="hidden">
                <svg class="w-8 h-8 text-emerald-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-sm font-medium text-emerald-700">Dosya Seç</span>
            </label>
        </div>

        <!-- Upload Status -->
        <div x-show="uploading" x-cloak class="flex items-center justify-center py-3 px-4 bg-zinc-50 border border-zinc-200 rounded-lg">
            <svg class="animate-spin h-5 w-5 text-emerald-600 mr-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm text-zinc-700">Yükleniyor...</span>
        </div>

        <div x-show="uploadSuccess" x-cloak class="py-3 px-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-lg">
            Belgeniz alındı, işleniyor...
        </div>

        <div x-show="uploadError" x-cloak class="py-3 px-4 bg-rose-50 border border-rose-200 text-rose-800 text-sm rounded-lg" x-text="uploadError">
        </div>
    </div>

    <!-- Belgeler List -->
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200">
        <div class="px-6 py-4 border-b border-zinc-200">
            <h2 class="text-lg font-semibold text-slate-800">Gönderilen Belgeler</h2>
        </div>

        @if($belgeler->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 text-zinc-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-zinc-500 text-sm">Henüz belge göndermediniz</p>
            </div>
        @else
            <div class="divide-y divide-zinc-200">
                @foreach($belgeler as $belge)
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-sm font-medium text-slate-800">
                                        {{ ucfirst($belge->belge_turu?->value ?? 'Belge') }}
                                    </span>
                                    @if($belge->durum === App\Enums\BelgeDurumu::Islendi)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            İşlendi
                                        </span>
                                    @elseif($belge->durum === App\Enums\BelgeDurumu::Bekliyor)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Bekliyor
                                        </span>
                                    @elseif($belge->durum === App\Enums\BelgeDurumu::KontrolGerekli)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            Kontrol
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                            Hata
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-500">{{ $belge->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($belgeler->hasPages())
                <div class="px-6 py-4 border-t border-zinc-200">
                    {{ $belgeler->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
