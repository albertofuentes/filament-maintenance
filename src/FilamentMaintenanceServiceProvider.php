<?php

namespace Albertofuentes\FilamentMaintenance;

use Albertofuentes\FilamentMaintenance\Commands\FilamentMaintenanceCommand;
use Albertofuentes\FilamentMaintenance\Livewire\MaintenanceSwitch;
use Albertofuentes\FilamentMaintenance\Testing\TestsFilamentMaintenance;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMaintenanceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-maintenance';

    public static string $viewNamespace = 'filament-maintenance';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('albertofuentes/filament-maintenance');
            });

        $configFileName = 'maintenance';

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile($configFileName);
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        if (app()->runningInConsole() && file_exists(__DIR__ . '/../config/maintenance.php')) {
            $this->publishes([
                __DIR__ . '/../config/maintenance.php' => config_path('maintenance.php'),
            ], 'filament-maintenance-config');
        }

        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-maintenance/{$file->getFilename()}"),
                ], 'filament-maintenance-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentMaintenance);

        Livewire::component('filament-maintenance-switch', MaintenanceSwitch::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'albertofuentes/filament-maintenance';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // Css::make('filament-maintenance-styles', __DIR__ . '/../resources/dist/filament-maintenance.css'),
            // Js::make('filament-maintenance-scripts', __DIR__ . '/../resources/dist/filament-maintenance.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentMaintenanceCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament_maintenance_tables',
        ];
    }
}
