<?php

namespace App\Models;

use App\Enums\BeyannameTipiAdi;
use App\Enums\Periyot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BeyannameTipi extends Model
{
    /** @use HasFactory<\Database\Factories\BeyannameTipiFactory> */
    use HasFactory;

    protected $table = 'beyanname_tipleri';

    protected $fillable = [
        'mukellef_id',
        'tip',
        'periyot',
        'son_gun',
    ];

    protected function casts(): array
    {
        return [
            'tip' => BeyannameTipiAdi::class,
            'periyot' => Periyot::class,
        ];
    }

    public function mukellef(): BelongsTo
    {
        return $this->belongsTo(Mukellef::class);
    }

    public function beyannameTakvim(): HasMany
    {
        return $this->hasMany(BeyannameTakvim::class, 'beyanname_tipi_id');
    }
}
