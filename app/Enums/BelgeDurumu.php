<?php

namespace App\Enums;

enum BelgeDurumu: string
{
    case Bekliyor = 'bekliyor';
    case Islendi = 'islendi';
    case Hata = 'hata';
    case KontrolGerekli = 'kontrol_gerekli';
}
