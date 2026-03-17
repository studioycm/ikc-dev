<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyUserRequestTopic: string implements HasColor, HasIcon, HasLabel
{
//    case ALUF = 'aluf';
    case ADVANCE_STUDY_REGISTRATION = 'advance_study_registration';
    case AGRA_FORM = 'agra_form';

    case BREEDING_ABROAD = 'breeding_abroad';
    case BREEDING_DOGS = 'breeding_dogs';

    case CHAMPION_DIPLOMA_REQUEST = 'champion_diploma_request';
    case MHG_CHECK = 'hg_form';
    case PELVIC_ELBOW_PHOTO_DECODING = 'Payment of pelvic / elbow photo decoding';
    case PEDIGREE_PAPER_REQUEST = 'pedigree_paper_request';
    case STUDY_REGISTRATION = 'study_registration';

    case YOUNG_RIDER_REGISTRATION = 'young_rider_registration';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADVANCE_STUDY_REGISTRATION => __('Advance Course Registration'),
            self::AGRA_FORM => __('Dog License Certificate'),
//            self::ALUF => __('Champion Certificate'),
            self::BREEDING_ABROAD => __('Litter Abroad'),
            self::BREEDING_DOGS => __('Approved Litter Dog'),
            self::CHAMPION_DIPLOMA_REQUEST => __('Champion Certificate Request'),
            self::MHG_CHECK => __('Breeding Assessment Test'),
            self::PELVIC_ELBOW_PHOTO_DECODING => __('Pelvic or Elbow Decoding Payment'),
            self::PEDIGREE_PAPER_REQUEST => __('Pedigree Print Request'),
            self::STUDY_REGISTRATION => __('Course Registration'),
            self::YOUNG_RIDER_REGISTRATION => __('Junior Handling Registration'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
//            self::ALUF => 'warning',
            self::CHAMPION_DIPLOMA_REQUEST => 'success',
            self::PELVIC_ELBOW_PHOTO_DECODING => 'danger',
            self::AGRA_FORM, self::MHG_CHECK => 'gray',
            self::STUDY_REGISTRATION, self::ADVANCE_STUDY_REGISTRATION => 'purple',
            self::BREEDING_ABROAD, self::BREEDING_DOGS => 'primary',
            self::PEDIGREE_PAPER_REQUEST => 'info',
            self::YOUNG_RIDER_REGISTRATION => 'yellow',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
//            self::ALUF                       => 'fas-award',
            self::ADVANCE_STUDY_REGISTRATION => 'fas-user-graduate',
            self::STUDY_REGISTRATION => 'fas-graduation-cap',
            self::AGRA_FORM => 'fas-certificate',
            self::BREEDING_ABROAD => 'fas-plane-departure',
            self::BREEDING_DOGS => 'fas-paw',
            self::CHAMPION_DIPLOMA_REQUEST => 'fas-trophy',
            self::MHG_CHECK => 'fas-clipboard-check',
            self::PELVIC_ELBOW_PHOTO_DECODING => 'fas-x-ray',
            self::PEDIGREE_PAPER_REQUEST => 'fas-print',
            self::YOUNG_RIDER_REGISTRATION => 'fas-child',
        };
    }
}
