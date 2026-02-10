<?php

namespace App\Http\Requests;

use App\Enums\BeyannameTipiAdi;
use App\Enums\IletisimKanali;
use App\Enums\MukellefTuru;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MukellefUpdateRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapma yetkisi var mı?
     */
    public function authorize(): bool
    {
        // Route'tan gelen mükellef'in user_id'si ile giriş yapmış kullanıcının id'si eşleşmeli
        return $this->route('mukellef')->user_id === $this->user()->id;
    }

    /**
     * İsteğe uygulanacak validasyon kuralları.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ad' => ['required', 'string', 'max:255'],
            'vkn' => ['nullable', 'string', 'digits:10'],
            'tckn' => ['nullable', 'string', 'digits:11'],
            'tur' => ['required', Rule::enum(MukellefTuru::class)],
            'telefon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'iletisim_kanali' => ['nullable', Rule::enum(IletisimKanali::class)],
            'telegram_chat_id' => ['nullable', 'string', 'max:255'],
            'aktif' => ['boolean'],
            'beyanname_tipleri' => ['nullable', 'array'],
            'beyanname_tipleri.*' => ['string', Rule::in(array_map(fn ($c) => $c->value, BeyannameTipiAdi::cases()))],
        ];
    }

    /**
     * Özel hata mesajları.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ad.required' => 'Mükellef adı zorunludur.',
            'vkn.digits' => 'VKN 10 haneli olmalıdır.',
            'tckn.digits' => 'TCKN 11 haneli olmalıdır.',
            'tur.required' => 'Mükellef türü seçilmelidir.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
        ];
    }
}
