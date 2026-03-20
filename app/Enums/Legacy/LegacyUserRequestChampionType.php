<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyUserRequestChampionType: string implements HasLabel, HasColor, HasIcon
{
    case JUNIOR_CHAMPION = '91';
    case CHAMPION = '3';
    case GRAND_CHAMPION = '131';
    case VETERAN_CHAMPION = '442';
    case WORK_BH = '265';
    case WORK_IGP1 = '493';
    case WORK_IGP2 = '494';
    case WORK_IGP3 = '495';
    case TRACKING_FH1 = '42';
    case TRACKING_FH2 = '273';
    case TRACKING_FH3 = '277';
    case MONDIORING_MR1 = '601';
    case MONDIORING_MR2 = '602';
    case MONDIORING_MR3 = '603';
    case AGILITY_AG1 = '529';
    case AGILITY_AG2 = '530';
    case AGILITY_AG3 = '545';
    case AGILITY_CHAMPION = '531';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::JUNIOR_CHAMPION => __('אלוף ישראל צעיר IL.JCH'),
            self::CHAMPION => __('אלוף ישראל IL.CH'),
            self::GRAND_CHAMPION => __('אלוף על ישראלי IL.GCH'),
            self::VETERAN_CHAMPION => __('אלוף קשיש IL.VCH'),
            self::WORK_BH => __('מעבר מבחן עבודה FCI BH'),
            self::WORK_IGP1 => __('עבודה FCI IGP1'),
            self::WORK_IGP2 => __('עבודה FCI IGP2'),
            self::WORK_IGP3 => __('עבודה FCI IGP3'),
            self::TRACKING_FH1 => __('גישוש FCI FH1'),
            self::TRACKING_FH2 => __('גישוש FCI FH2'),
            self::TRACKING_FH3 => __('גישוש FCI FH3'),
            self::MONDIORING_MR1 => __('מונדיאורינג MR1'),
            self::MONDIORING_MR2 => __('מונדיאורינג MR2'),
            self::MONDIORING_MR3 => __('מונדיאורינג MR3'),
            self::AGILITY_AG1 => __('אג’יליטי AG1'),
            self::AGILITY_AG2 => __('אג’יליטי AG2'),
            self::AGILITY_AG3 => __('אג’יליטי AG3'),
            self::AGILITY_CHAMPION => __('אג’יליטי AG.CH'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::GRAND_CHAMPION, self::AGILITY_CHAMPION => 'success',
            self::CHAMPION, self::JUNIOR_CHAMPION => 'primary',
            self::VETERAN_CHAMPION => 'warning',
            self::WORK_BH, self::WORK_IGP1, self::WORK_IGP2, self::WORK_IGP3 => 'info',
            self::TRACKING_FH1, self::TRACKING_FH2, self::TRACKING_FH3 => 'info',
            self::MONDIORING_MR1, self::MONDIORING_MR2, self::MONDIORING_MR3 => 'danger',
            self::AGILITY_AG1, self::AGILITY_AG2, self::AGILITY_AG3 => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::JUNIOR_CHAMPION => 'fas-user-graduate',
            self::CHAMPION => 'fas-award',
            self::GRAND_CHAMPION => 'fas-crown',
            self::VETERAN_CHAMPION => 'fas-hourglass-half',
            self::WORK_BH, self::WORK_IGP1, self::WORK_IGP2, self::WORK_IGP3 => 'fas-dumbbell',
            self::TRACKING_FH1, self::TRACKING_FH2, self::TRACKING_FH3 => 'fas-search-location',
            self::MONDIORING_MR1, self::MONDIORING_MR2, self::MONDIORING_MR3 => 'fas-shield-dog',
            self::AGILITY_AG1, self::AGILITY_AG2, self::AGILITY_AG3 => 'fas-running',
            self::AGILITY_CHAMPION => 'fas-trophy',
        };
    }

}
