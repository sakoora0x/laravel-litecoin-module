<?php

namespace Mollsoft\LaravelLitecoinModule\Facades;

use Illuminate\Support\Facades\Facade;

class Litecoin extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mollsoft\LaravelLitecoinModule\Litecoin::class;
    }
}
