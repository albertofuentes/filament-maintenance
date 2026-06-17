<?php

use Albertofuentes\FilamentMaintenance\Http\Middleware\PreventAccessDuringMaintenance;
use Albertofuentes\FilamentMaintenance\Support\IpMatcher;
use Albertofuentes\FilamentMaintenance\Support\MaintenanceManager;

it('falls back to the package maintenance view when no configured view is available', function (): void {
    config()->set('maintenance.view', null);

    $middleware = new PreventAccessDuringMaintenance(new MaintenanceManager(new IpMatcher));
    $method = new ReflectionMethod($middleware, 'viewName');

    expect($method->invoke($middleware, null))
        ->toBe('filament-maintenance::maintenance.default')
        ->and($method->invoke($middleware, '  '))
        ->toBe('filament-maintenance::maintenance.default');
});

it('uses a valid configured or custom maintenance view', function (): void {
    config()->set('maintenance.view', 'app::maintenance');

    $middleware = new PreventAccessDuringMaintenance(new MaintenanceManager(new IpMatcher));
    $method = new ReflectionMethod($middleware, 'viewName');

    expect($method->invoke($middleware, null))
        ->toBe('app::maintenance')
        ->and($method->invoke($middleware, ' custom::maintenance '))
        ->toBe('custom::maintenance');
});
