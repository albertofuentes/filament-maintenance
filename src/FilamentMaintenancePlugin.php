<?php

namespace Albertofuentes\FilamentMaintenance;

use Albertofuentes\FilamentMaintenance\Http\Middleware\PreventAccessDuringMaintenance;
use Albertofuentes\FilamentMaintenance\Pages\MaintenanceSettings;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class FilamentMaintenancePlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-maintenance';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                MaintenanceSettings::class,
            ])
            ->middleware([
                PreventAccessDuringMaintenance::class,
            ], isPersistent: true)
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => Blade::render('@livewire(\'filament-maintenance-switch\')'),
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
