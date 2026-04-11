<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyUserRecordType: string implements HasColor, HasIcon, HasLabel
{
    case NATIVE = 'Native';
    case OWNERS = 'Owners';
    case MEMBERS = 'Members';

    public function getLabel(): string
    {
        return match ($this) {
            self::NATIVE => __('Native'),
            self::OWNERS => __('Owners'),
            self::MEMBERS => __('Members'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::NATIVE => 'success',
            self::OWNERS => 'info',
            self::MEMBERS => 'purple',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NATIVE => 'fas-user-tie',
            self::OWNERS => 'fas-user-md',
            self::MEMBERS => 'fas-user-check',
        };
    }

}
