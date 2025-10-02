<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DecimalBooleanCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): bool
    {
        // Convert the decimal value from the DB into a boolean.
        // Any value of 1.0 or greater is true, everything else is false.
        return (float)$value >= 1.0;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): float
    {
        // Convert the boolean from the Toggle into 1.0 or 0.0 for the DB.
        return $value ? 1.0 : 0.0;
    }
}
