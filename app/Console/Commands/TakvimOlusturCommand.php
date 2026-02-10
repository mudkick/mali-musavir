<?php

namespace App\Console\Commands;

use App\Services\BeyannameTakvimService;
use Illuminate\Console\Command;

class TakvimOlusturCommand extends Command
{
    protected $signature = 'takvim:olustur {donem? : Dönem formatı YYYY-MM (varsayılan: gelecek ay)}';

    protected $description = 'Verilen dönem için beyanname takvim kayıtları oluşturur';

    public function handle(BeyannameTakvimService $service): int
    {
        $donem = $this->argument('donem');

        $this->info('Takvim oluşturuluyor: '.($donem ?? 'gelecek ay').'...');

        $count = $service->generateMonthlyCalendar($donem);

        $this->info("{$count} takvim kaydı oluşturuldu.");

        return Command::SUCCESS;
    }
}
