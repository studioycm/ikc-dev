<?php

namespace App\Enums\Legacy;

enum LegacySagirPrefix: int
{
    case ISR = 1;
    case IMP = 2;
    case APX = 3;
    case EXT = 4;
    case NUL = 5;

    public function code(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ISR => 'ISR',
            self::IMP => 'IMP',
            self::APX => 'APX',
            self::EXT => 'EXT',
            self::NUL => 'NUL',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ISR => 'blue',
            self::IMP => 'purple',
            self::APX => 'yellow',
            self::EXT => 'green',
            self::NUL => 'red',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ISR => 'fas-star-of-david',
            self::IMP => 'fas-globe',
            self::APX => 'fas-plus-square',
            self::EXT => 'fas-external-link-square-alt',
            self::NUL => 'fas-window-close',
        };
    }

    public static function options(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->getLabel();
        }

        return $out;
    }

    public function description(): string
    {
        return match ($this) {
            self::ISR => 'Israeli',
            self::IMP => 'Imported',
            self::APX => 'Apex',
            self::EXT => 'External',
            self::NUL => 'No prefix',
        };
    }

    // colors array for filament form input options
    public static function colors(): array
    {
        $out = [];
        foreach (self::cases() as $case) {
            $out[$case->value] = $case->getColor();
        }

        return $out;
    }
}
