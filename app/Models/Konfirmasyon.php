<?php

namespace App\Models;

use App\Enums\KonfirmasyonYaniti;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Konfirmasyon extends Model
{
    /** @use HasFactory<\Database\Factories\KonfirmasyonFactory> */
    use HasFactory;

    protected $table = 'konfirmasyonlar';

    protected $fillable = [
        'mukellef_id',
        'donem',
        'mesaj_metni',
        'gonderim_tarihi',
        'yanit',
        'beklenen_belge_sayisi',
        'yanit_tarihi',
    ];

    protected function casts(): array
    {
        return [
            'gonderim_tarihi' => 'datetime',
            'yanit' => KonfirmasyonYaniti::class,
            'yanit_tarihi' => 'datetime',
        ];
    }

    public function mukellef(): BelongsTo
    {
        return $this->belongsTo(Mukellef::class);
    }
}
