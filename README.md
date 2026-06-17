# Filament Maintenance

Filament Maintenance lets you enable maintenance mode per Filament panel, with access exceptions for IP addresses, CIDR ranges, manager users, and Spatie Permission roles.

## Installation

```bash
composer require albertofuentes/filament-maintenance
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="filament-maintenance-migrations"
php artisan migrate
```

Optionally publish the config and views:

```bash
php artisan vendor:publish --tag="filament-maintenance-config"
php artisan vendor:publish --tag="filament-maintenance-views"
```

The config file is published as `config/filament-maintenance.php`.

## Usage

Register the plugin in the Filament panel where you want maintenance mode:

```php
use Albertofuentes\FilamentMaintenance\FilamentMaintenancePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(FilamentMaintenancePlugin::make());
}
```

The plugin adds:

- A maintenance switch near the user menu for allowed managers.
- A `maintenance` settings page inside the panel.
- Middleware that blocks the panel when maintenance is enabled.
- A customizable 503 maintenance view.
- Audit events for enable, disable and settings updates.

## Permissions

Managers can be configured from the settings page by selecting users or Spatie role names. The package also allows users with the Laravel permission `manage-filament-maintenance`.

While no manager users or manager roles are configured for a panel, authenticated users can access the settings page so the first configuration can be completed.

## Testing

```bash
composer test
```
