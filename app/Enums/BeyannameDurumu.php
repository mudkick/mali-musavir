<?php

namespace App\Enums;

enum BeyannameDurumu: string
{
    case Bekliyor = 'bekliyor';
    case Hazirlaniyor = 'hazirlaniyor';
    case Hazir = 'hazir';
    case Verildi = 'verildi';
}
