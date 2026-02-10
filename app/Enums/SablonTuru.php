<?php

namespace App\Enums;

enum SablonTuru: string
{
    case EksikBelge = 'eksik_belge';
    case BeyannameOnay = 'beyanname_onay';
    case OdemeHatirlatma = 'odeme_hatirlatma';
    case Genel = 'genel';
}
