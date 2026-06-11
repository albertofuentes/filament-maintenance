<?php

namespace Albertofuentes\FilamentMaintenance\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceEvent extends Model
{
    protected $table = 'filament_maintenance_events';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
    ];
}
