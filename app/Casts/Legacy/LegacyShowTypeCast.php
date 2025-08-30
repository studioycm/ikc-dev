<?php

namespace App\Casts\Legacy;

use App\Enums\Legacy\LegacyShowTypeEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class LegacyShowTypeCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): LegacyShowTypeEnum
    {
        // MariaDB DECIMAL may arrive as string "1.00000000" etc.
        $int = is_numeric($value) ? (int)$value : 0;

        return match ($int) {
            1 => LegacyShowTypeEnum::International,
            2 => LegacyShowTypeEnum::Clubs,
            3 => LegacyShowTypeEnum::National,
            4 => LegacyShowTypeEnum::BreedingQualificationTest,
            default => LegacyShowTypeEnum::NotSet,
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        // Accept enum|int|string|null and normalize to 0..4
        if ($value instanceof LegacyShowTypeEnum) {
            return $value->value;
        }

        if (is_numeric($value)) {
            $int = (int)$value;

            return in_array($int, [0, 1, 2, 3, 4], true) ? $int : 0;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            return match ($normalized) {
                'international', '1' => LegacyShowTypeEnum::International->value,
                'clubs', 'club', '2' => LegacyShowTypeEnum::Clubs->value,
                'national', '3' => LegacyShowTypeEnum::National->value,
                'breeding qualification test', 'bqt', '4' => LegacyShowTypeEnum::BreedingQualificationTest->value,
                default => 0,
            };
        }

        return 0;
    }
}
