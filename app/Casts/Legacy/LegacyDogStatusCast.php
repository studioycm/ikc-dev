<?php

namespace App\Casts\Legacy;

use App\Enums\Legacy\LegacyDogStatus;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class LegacyDogStatusCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): LegacyDogStatus
    {
        // Treat null / empty / invalid as Unknown
        $raw = is_string($value) ? trim($value) : null;
        if ($raw === null || $raw === '') {
            return LegacyDogStatus::Off;
        }

        $normalized = strtolower($raw);

        // Support known variants
        return match ($normalized) {
            'notapproved', 'not approved' => LegacyDogStatus::NotApproved,
            'notrecomm', 'not recommended' => LegacyDogStatus::NotRecommended,
            'onhold', 'on hold' => LegacyDogStatus::OnHold,
            'waiting' => LegacyDogStatus::Waiting,
            default => LegacyDogStatus::Off,
        };
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        // Accept enum|string|null; normalize to a valid stored string or null
        if ($value instanceof LegacyDogStatus) {
            return $value->value;
        }

        if (is_string($value)) {
            $normalized = strtolower(trim($value));

            return match ($normalized) {
                'notapproved', 'not approved' => LegacyDogStatus::NotApproved->value,
                'notrecomm', 'not recommended' => LegacyDogStatus::NotRecommended->value,
                'onhold', 'on hold' => LegacyDogStatus::OnHold->value,
                'waiting' => LegacyDogStatus::Waiting->value,
                'Off' => LegacyDogStatus::Off->value,
                default => null,
            };
        }

        // Null, empty, or unsupported types -> store null (keeps legacy DB intact)
        return null;
    }
}
