<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyDogGender: int implements HasColor, HasIcon, HasLabel
{
    case Male = 1;
    case Female = 2;
    case Unknown = 0;

    public function getLabel(): string
    {
        return $this->name;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Male => 'blue',
            self::Female => 'pink',
            self::Unknown => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Male => 'fas-mars',
            self::Female => 'fas-venus',
            self::Unknown => 'fas-question',
        };
    }

    //    public static function options(): array
    //    {
    //        $out = [];
    //        foreach (self::cases() as $case) {
    //            $out[$case->value] = $case->getLabel();
    //        }
    //
    //        return $out;
    //    }
}
