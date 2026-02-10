<?php

namespace App\Models;

use App\Enums\BeyannameDurumu;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeyannameTakvim extends Model
{
    /** @use HasFactory<\Database\Factories\BeyannameTakvimFactory> */
    use HasFactory;

    protected $table = 'beyanname_takvim';

    protected $fillable = [
        'mukellef_id',
        'beyanname_tipi_id',
        'donem',
        'son_tarih',
        'durum',
    ];

    protected function casts(): array
    {
        return [
            'son_tarih' => 'date',
            'durum' => BeyannameDurumu::class,
        ];
    }

    public function mukellef(): BelongsTo
    {
        return $this->belongsTo(Mukellef::class);
    }

    public function beyannameTipi(): BelongsTo
    {
        return $this->belongsTo(BeyannameTipi::class);
    }
}
