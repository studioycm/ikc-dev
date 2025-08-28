<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyPedigreeColor: string implements HasColor, HasIcon, HasLabel
{
    case Blue = 'Blue';
    case Red = 'Red';

    public function getLabel(): string
    {
        return match ($this) {
            self::Blue => __('Blue'),
            self::Red => __('Red'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Blue => 'info',
            self::Red => 'danger',
        };
    }

    public function getIcon(): string
    {
        return 'fas-certificate';
    }

    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->getLabel();
        }

        return $out;
    }
}
