<?php

namespace Albertofuentes\FilamentMaintenance\Commands;

use Illuminate\Console\Command;

class FilamentMaintenanceCommand extends Command
{
    public $signature = 'filament-maintenance';

    public $description = 'Manage Filament maintenance mode';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
