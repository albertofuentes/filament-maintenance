<?php

namespace Albertofuentes\FilamentMaintance\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Albertofuentes\FilamentMaintance\FilamentMaintance
 */
class FilamentMaintance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Albertofuentes\FilamentMaintance\FilamentMaintance::class;
    }
}
