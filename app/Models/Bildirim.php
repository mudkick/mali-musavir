<?php

namespace App\Models;

use App\Enums\BildirimDurumu;
use App\Enums\IletisimKanali;
use App\Enums\SablonTuru;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bildirim extends Model
{
    /** @use HasFactory<\Database\Factories\BildirimFactory> */
    use HasFactory;

    protected $table = 'bildirimler';

    protected $fillable = [
        'mukellef_id',
        'sablon_turu',
        'mesaj_metni',
        'kanal',
        'durum',
        'gonderim_tarihi',
    ];

    protected function casts(): array
    {
        return [
            'sablon_turu' => SablonTuru::class,
            'kanal' => IletisimKanali::class,
            'durum' => BildirimDurumu::class,
            'gonderim_tarihi' => 'datetime',
        ];
    }

    public function mukellef(): BelongsTo
    {
        return $this->belongsTo(Mukellef::class);
    }
}
