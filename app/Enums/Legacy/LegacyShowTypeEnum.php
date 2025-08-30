<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyShowTypeEnum: int implements HasColor, HasIcon, HasLabel
{
    case NotSet = 0;
    case International = 1;
    case Clubs = 2;
    case National = 3;
    case BreedingQualificationTest = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::International => __('International Show'),
            self::Clubs => __('Clubs Show'),
            self::National => __('National Show'),
            self::BreedingQualificationTest => __('Breeding Qualification Test'),
            self::NotSet => __('Not set'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::International => 'success',
            self::Clubs => 'purple',
            self::National => 'blue',
            self::BreedingQualificationTest => 'warning',
            self::NotSet => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::International => 'fas-globe',
            self::Clubs => 'fas-flag',
            self::National => 'fas-star-of-david',
            self::BreedingQualificationTest => 'fas-clipboard-list',
            self::NotSet => 'fas-question',
        };
    }
}
