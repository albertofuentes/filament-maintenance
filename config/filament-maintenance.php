<?php

use Albertofuentes\FilamentMaintenance\Models\MaintenanceEvent;
use Albertofuentes\FilamentMaintenance\Models\MaintenanceSetting;

return [
    /*
     * Model used to store the current maintenance state per Filament panel.
     */
    'setting_model' => MaintenanceSetting::class,

    /*
     * Model used to store audit events.
     */
    'event_model' => MaintenanceEvent::class,

    /*
     * Fallback content for new panel settings.
     */
    'default_title' => 'Panel under maintenance',
    'default_message' => 'We are performing maintenance. Please try again later.',

    /*
     * Blade view rendered when a visitor cannot bypass maintenance.
     */
    'view' => 'filament-maintenance::maintenance.default',

    /*
     * Optional callback hooks. Each callback receives the authenticated user.
     */
    'can_manage_using' => null,
    'can_bypass_using' => null,
];
