<?php

namespace sakoora0x\LaravelLitecoinModule\Facades;

use Illuminate\Support\Facades\Facade;

class Litecoin extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \sakoora0x\LaravelLitecoinModule\Litecoin::class;
    }
}
