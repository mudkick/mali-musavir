<?php

namespace App\Models;

use App\Enums\IletisimKanali;
use App\Enums\MukellefTuru;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mukellef extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\MukellefFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'ad',
        'vkn',
        'tckn',
        'tur',
        'telefon',
        'email',
        'iletisim_kanali',
        'telegram_chat_id',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'tur' => MukellefTuru::class,
            'iletisim_kanali' => IletisimKanali::class,
            'aktif' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function belgeler(): HasMany
    {
        return $this->hasMany(Belge::class, 'mukellef_id');
    }

    public function beyannameTipleri(): HasMany
    {
        return $this->hasMany(BeyannameTipi::class, 'mukellef_id');
    }

    public function beyannameTakvim(): HasMany
    {
        return $this->hasMany(BeyannameTakvim::class, 'mukellef_id');
    }

    public function konfirmasyonlar(): HasMany
    {
        return $this->hasMany(Konfirmasyon::class, 'mukellef_id');
    }

    public function bildirimler(): HasMany
    {
        return $this->hasMany(Bildirim::class, 'mukellef_id');
    }
}
