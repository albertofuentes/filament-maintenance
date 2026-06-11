<?php

namespace Albertofuentes\FilamentMaintenance\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Albertofuentes\FilamentMaintenance\FilamentMaintenance
 */
class FilamentMaintenance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Albertofuentes\FilamentMaintenance\FilamentMaintenance::class;
    }
}
