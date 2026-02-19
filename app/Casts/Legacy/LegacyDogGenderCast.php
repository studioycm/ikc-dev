<?php

namespace App\Casts\Legacy;

use App\Enums\Legacy\LegacyDogGender;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class LegacyDogGenderCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): LegacyDogGender
    {
        // MariaDB DECIMAL comes back as strings like "1.00000000".
        // Map to 1/2/0; anything else becomes Unknown (0).
        $int = is_numeric($value) ? (int)$value : 0;

        return match ($int) {
            1 => LegacyDogGender::Male,
            2 => LegacyDogGender::Female,
            default => LegacyDogGender::Missing,
        };

    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        // Accept enum|int|string|null and normalize to 0/1/2.
        if ($value instanceof LegacyDogGender) {
            return $value->value;
        }

        if (is_numeric($value)) {
            $int = (int)$value;

            return in_array($int, [1, 2], true) ? $int : 0;
        }

        return 0;
    }
}
