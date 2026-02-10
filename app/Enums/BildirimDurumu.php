<?php

namespace App\Enums;

enum BildirimDurumu: string
{
    case Bekliyor = 'bekliyor';
    case Gonderildi = 'gonderildi';
    case Basarisiz = 'basarisiz';
}
