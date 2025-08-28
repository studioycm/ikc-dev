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
            self::APX => 'orange',
            self::EXT => 'green',
            self::NUL => 'red',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::ISR => 'fas-user-tie',
            self::IMP => 'fas-user-graduate',
            self::APX => 'fas-user-ninja',
            self::EXT => 'fas-user-secret',
            self::NUL => 'fas-user-slash',
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
}
