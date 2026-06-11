<?php

namespace Albertofuentes\FilamentMaintance\Commands;

use Illuminate\Console\Command;

class FilamentMaintanceCommand extends Command
{
    public $signature = 'filament-maintance';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
