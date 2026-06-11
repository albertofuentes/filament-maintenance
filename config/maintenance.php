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
    'default_title' => 'Panel en mantenimiento',
    'default_message' => 'Estamos realizando tareas de mantenimiento. Vuelve a intentarlo mas tarde.',

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
