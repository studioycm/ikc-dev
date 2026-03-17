<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyUserRequestPaperType: string implements HasColor, HasIcon, HasLabel
{
    case PEDIGREE_COPY = '1';
    case PEDIGREE_EXPORT = '2';

    public function getLabel(): string
    {
        return match ($this) {
            self::PEDIGREE_COPY => __('Pedigree Copy'),
            self::PEDIGREE_EXPORT => __('Pedigree Export'),
        };
    }


    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PEDIGREE_COPY => 'info',
            self::PEDIGREE_EXPORT => 'purple',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::PEDIGREE_COPY => 'fas-file-signature',
            self::PEDIGREE_EXPORT => 'fas-file-export',
        };
    }
}
