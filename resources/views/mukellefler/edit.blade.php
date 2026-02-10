@extends('layouts.dashboard')

@section('title', $mukellef->ad . ' — Düzenle')

@section('content')
    <div x-data="{ iletisimKanali: '{{ old('iletisim_kanali', $mukellef->iletisim_kanali?->value) }}' }">
        <form method="POST" action="{{ route('mukellefler.update', $mukellef) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sol Sütun: Temel Bilgiler -->
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
                    <h2 class="text-lg font-semibold text-slate-800 mb-6">Temel Bilgiler</h2>

                    <!-- Ad -->
                    <div class="mb-6">
                        <label for="ad" class="block text-sm font-medium text-zinc-700 mb-2">Ad <span class="text-rose-600">*</span></label>
                        <input type="text"
                               id="ad"
                               name="ad"
                               value="{{ old('ad', $mukellef->ad) }}"
                               required
                               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="Mükellef adı">
                        @error('ad')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VKN -->
                    <div class="mb-6">
                        <label for="vkn" class="block text-sm font-medium text-zinc-700 mb-2">VKN</label>
                        <input type="text"
                               id="vkn"
                               name="vkn"
                               value="{{ old('vkn', $mukellef->vkn) }}"
                               maxlength="10"
                               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="10 haneli">
                        @error('vkn')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TCKN -->
                    <div class="mb-6">
                        <label for="tckn" class="block text-sm font-medium text-zinc-700 mb-2">TCKN</label>
                        <input type="text"
                               id="tckn"
                               name="tckn"
                               value="{{ old('tckn', $mukellef->tckn) }}"
                               maxlength="11"
                               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="11 haneli">
                        @error('tckn')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tür -->
                    <div class="mb-6">
                        <label for="tur" class="block text-sm font-medium text-zinc-700 mb-2">Tür</label>
                        <select id="tur"
                                name="tur"
                                class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Seçin...</option>
                            @foreach($mukellefTurleri as $tur)
                                <option value="{{ $tur->value }}" {{ old('tur', $mukellef->tur?->value) === $tur->value ? 'selected' : '' }}>
                                    {{ ucfirst($tur->value) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tur')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Aktif -->
                    <div class="mb-0">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"
                                   name="aktif"
                                   value="1"
                                   {{ old('aktif', $mukellef->aktif ? '1' : '0') === '1' ? 'checked' : '' }}
                                   class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-3 text-sm font-medium text-zinc-700">Aktif</span>
                        </label>
                        @error('aktif')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sağ Sütun: İletişim Bilgileri -->
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
                    <h2 class="text-lg font-semibold text-slate-800 mb-6">İletişim Bilgileri</h2>

                    <!-- Telefon -->
                    <div class="mb-6">
                        <label for="telefon" class="block text-sm font-medium text-zinc-700 mb-2">Telefon</label>
                        <input type="text"
                               id="telefon"
                               name="telefon"
                               value="{{ old('telefon', $mukellef->telefon) }}"
                               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="+90 555 123 4567">
                        @error('telefon')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- E-posta -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-zinc-700 mb-2">E-posta</label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $mukellef->email) }}"
                               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="ornek@domain.com">
                        @error('email')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- İletişim Kanalı -->
                    <div class="mb-6">
                        <label for="iletisim_kanali" class="block text-sm font-medium text-zinc-700 mb-2">İletişim Kanalı</label>
                        <select id="iletisim_kanali"
                                name="iletisim_kanali"
                                x-model="iletisimKanali"
                                class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Seçin...</option>
                            @foreach($iletisimKanallari as $kanal)
                                <option value="{{ $kanal->value }}">
                                    {{ ucfirst($kanal->value) }}
                                </option>
                            @endforeach
                        </select>
                        @error('iletisim_kanali')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telegram Chat ID (Conditional) -->
                    <div x-show="iletisimKanali === 'telegram'"
                         x-cloak
                         class="mb-0">
                        <label for="telegram_chat_id" class="block text-sm font-medium text-zinc-700 mb-2">Telegram Chat ID</label>
                        <input type="text"
                               id="telegram_chat_id"
                               name="telegram_chat_id"
                               value="{{ old('telegram_chat_id', $mukellef->telegram_chat_id) }}"
                               class="w-full rounded-lg border-zinc-300 focus:border-emerald-500 focus:ring-emerald-500"
                               placeholder="123456789">
                        @error('telegram_chat_id')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Alt Kısım: Beyanname Tipleri -->
            <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6 mt-6">
                <h2 class="text-lg font-semibold text-slate-800 mb-6">Beyanname Tipleri</h2>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($beyannameTipleri as $tip)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox"
                                   name="beyanname_tipleri[]"
                                   value="{{ $tip->value }}"
                                   {{ in_array($tip->value, old('beyanname_tipleri', $mevcutBeyannameTipleri)) ? 'checked' : '' }}
                                   class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-3 text-sm text-zinc-700">
                                {{ ucfirst(str_replace('_', ' ', $tip->value)) }}
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('beyanname_tipleri')
                    <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Butonları -->
            <div class="flex items-center justify-end space-x-3 mt-6">
                <a href="{{ route('mukellefler.index') }}"
                   class="px-4 py-2 bg-white border border-zinc-300 text-zinc-700 text-sm font-medium rounded-lg hover:bg-zinc-50 transition-colors">
                    İptal
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    Güncelle
                </button>
            </div>
        </form>
    </div>
@endsection
