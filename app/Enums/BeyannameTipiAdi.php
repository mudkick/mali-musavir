<?php

namespace App\Enums;

enum BeyannameTipiAdi: string
{
    case Kdv = 'kdv';
    case Muhtasar = 'muhtasar';
    case GeciciVergi = 'gecici_vergi';
    case BaBs = 'ba_bs';
    case YillikGelir = 'yillik_gelir';
    case YillikKurumlar = 'yillik_kurumlar';
}
