<?php

namespace App\Enums;

enum IletisimKanali: string
{
    case Telegram = 'telegram';
    case Whatsapp = 'whatsapp';
    case Pwa = 'pwa';
}
