<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyDogStatus: string implements HasColor, HasIcon, HasLabel
{
    case NotApproved = 'notapproved';
    case NotRecommended = 'notrecomm';
    case OnHold = 'onhold';
    case Waiting = 'waiting';
    // handle empty/null/none of the above values the right way
    case Unknown = 'unknown';


    public function getLabel(): string
    {
        return match ($this) {
            self::NotApproved => __('Not Approved'),
            self::NotRecommended => __('Not Recommended'),
            self::OnHold => __('On Hold'),
            self::Waiting => __('Waiting'),
            self::Unknown => "---",
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NotApproved => 'danger',
            self::NotRecommended => 'warning',
            self::OnHold => 'white',
            self::Waiting => 'info',
            self::Unknown => 'grey',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NotApproved => 'fas-ban',
            self::NotRecommended => 'fas-thumbs-down',
            self::OnHold => 'fas-pause-circle',
            self::Waiting => 'fas-hourglass-half',
            self::Unknown => 'fas-question',
        };
    }
}
