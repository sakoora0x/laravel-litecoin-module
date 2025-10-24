<?php

namespace sakoora0x\LaravelLitecoinModule\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use sakoora0x\LaravelLitecoinModule\Support\DecimalNumber;

class DecimalCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): DecimalNumber
    {
        return new DecimalNumber((string)($value ?: 0));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof DecimalNumber) {
            return $value->toString();
        }

        // Support legacy Decimal\Decimal if it's still being used
        if (is_object($value) && method_exists($value, 'toString')) {
            return $value->toString();
        }

        return $value;
    }
}
