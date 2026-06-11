<?php

namespace Albertofuentes\FilamentMaintenance\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bool $enabled
 * @property array<int, string>|null $allowed_ips
 * @property array<int, string>|null $allowed_roles
 * @property array<int, int|string>|null $manager_user_ids
 * @property array<int, string>|null $manager_roles
 * @property string|null $title
 * @property string|null $message
 * @property string|null $view
 */
class MaintenanceSetting extends Model
{
    protected $table = 'filament_maintenance_settings';

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'allowed_ips' => 'array',
        'allowed_roles' => 'array',
        'manager_user_ids' => 'array',
        'manager_roles' => 'array',
        'enabled_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];
}
