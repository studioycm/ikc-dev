<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyDogSize: int implements HasColor, HasIcon, HasLabel
{
    case Unknown = 0;
    case Petite = 1;
    case Small = 2;
    case Medium = 3;
    case Large = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::Petite => __('Petite'),
            self::Small => __('Small'),
            self::Medium => __('Medium'),
            self::Large => __('Large'),
            self::Unknown => __('w/o'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Petite => 'info',
            self::Small => 'primary',
            self::Medium => 'success',
            self::Large => 'warning',
            self::Unknown => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Petite => 'heroicon-m-sparkles',
            self::Small => 'heroicon-m-arrows-pointing-in',
            self::Medium => 'heroicon-m-arrows-right-left',
            self::Large => 'heroicon-m-arrows-pointing-out',
            self::Unknown => 'fas-ban',
        };
    }
}
