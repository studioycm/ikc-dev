<?php

namespace App\Casts\Legacy;

use App\Enums\Legacy\LegacyDogSize;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class LegacyDogSizeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): LegacyDogSize
    {
        // MariaDB DECIMAL comes back as strings like "1.00000000".
        // Map to 1..4; anything else becomes Unknown (0).
        $int = is_numeric($value) ? (int)$value : 0;

        return match ($int) {
            1 => LegacyDogSize::Petite,
            2 => LegacyDogSize::Small,
            3 => LegacyDogSize::Medium,
            4 => LegacyDogSize::Large,
            default => LegacyDogSize::Unknown,
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        // Accept enum|int|string|null and normalize to 0..4.
        if ($value instanceof LegacyDogSize) {
            return $value->value;
        }

        if (is_numeric($value)) {
            $int = (int)$value;

            return in_array($int, [0, 1, 2, 3, 4], true) ? $int : 0;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            return match ($normalized) {
                'petite' => LegacyDogSize::Petite->value,
                'small' => LegacyDogSize::Small->value,
                'medium' => LegacyDogSize::Medium->value,
                'large', 'big' => LegacyDogSize::Large->value,
                default => 0,
            };
        }

        return 0;
    }
}
