<?php

namespace App\Models;

use App\Enums\BelgeDurumu;
use App\Enums\BelgeTuru;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Belge extends Model
{
    /** @use HasFactory<\Database\Factories\BelgeFactory> */
    use HasFactory;

    protected $table = 'belgeler';

    protected $fillable = [
        'mukellef_id',
        'gorsel_path',
        'extracted_data',
        'durum',
        'confidence',
        'belge_turu',
    ];

    protected function casts(): array
    {
        return [
            'extracted_data' => 'array',
            'durum' => BelgeDurumu::class,
            'confidence' => 'float',
            'belge_turu' => BelgeTuru::class,
        ];
    }

    public function mukellef(): BelongsTo
    {
        return $this->belongsTo(Mukellef::class);
    }
}
