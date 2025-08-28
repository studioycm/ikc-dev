<?php

namespace App\Enums\Legacy;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum LegacyShowClass: string implements HasColor, HasIcon, HasLabel
{
    // 8 classes × gender (16 total), string values match raw ClassName values exactly
    case VeteranFemale = 'Veteran Class Female';
    case VeteranMale = 'Veteran Class Male';

    case ChampionFemale = 'Champion Class Female';
    case ChampionMale = 'Champion Class Male';

    case OpenFemale = 'Open Class Female';
    case OpenMale = 'Open Class Male';

    case WorkingFemale = 'Working Class Female';
    case WorkingMale = 'Working Class Male';

    case IntermediateFemale = 'Intermediate Class Female';
    case IntermediateMale = 'Intermediate Class Male';

    case Puppy6To9Male = 'Puppy Class 6-9 Male';
    case Puppy6To9Female = 'Puppy Class 6-9 Female';

    case JuniorFemale = 'Junior Class Female';
    case JuniorMale = 'Junior Class Male';

    case Baby3To6Female = 'Baby Class 3-6 Female';
    case Baby3To6Male = 'Baby Class 3-6 Male';

    public function getLabel(): string
    {
        return match ($this) {
            self::VeteranFemale => __('Veteran Class Female'),
            self::VeteranMale => __('Veteran Class Male'),

            self::ChampionFemale => __('Champion Class Female'),
            self::ChampionMale => __('Champion Class Male'),

            self::OpenFemale => __('Open Class Female'),
            self::OpenMale => __('Open Class Male'),

            self::WorkingFemale => __('Working Class Female'),
            self::WorkingMale => __('Working Class Male'),

            self::IntermediateFemale => __('Intermediate Class Female'),
            self::IntermediateMale => __('Intermediate Class Male'),

            self::Puppy6To9Male => __('Puppy Class 6-9 Male'),
            self::Puppy6To9Female => __('Puppy Class 6-9 Female'),

            self::JuniorFemale => __('Junior Class Female'),
            self::JuniorMale => __('Junior Class Male'),

            self::Baby3To6Female => __('Baby Class 3-6 Female'),
            self::Baby3To6Male => __('Baby Class 3-6 Male'),
        };
    }

    public function getIcon(): string
    {
        // Use consistent icons per group
        return match ($this->group()) {
            'baby' => 'fas-baby',
            'puppy' => 'fas-paw',
            'junior' => 'fas-paw',
            'intermediate' => 'fas-paw',
            'open' => 'fas-paw',
            'working' => 'fas-briefcase',
            'champion' => 'fas-crown',
            'veteran' => 'fas-star',
            default => 'fas-paw',
        };
    }

    public function getColor(): string
    {
        // Filament palette: primary | success | warning | danger | info | gray
        return match ($this->group()) {
            'baby' => 'info',
            'puppy' => 'success',
            'junior' => 'primary',
            'intermediate' => 'warning',
            'open' => 'gray',
            'working' => 'primary',
            'champion' => 'success',
            'veteran' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Helper to group cases by class family.
     */
    public function group(): string
    {
        return match ($this) {
            self::Baby3To6Female, self::Baby3To6Male => 'baby',
            self::Puppy6To9Male, self::Puppy6To9Female => 'puppy',
            self::JuniorFemale, self::JuniorMale => 'junior',
            self::IntermediateFemale, self::IntermediateMale => 'intermediate',
            self::OpenFemale, self::OpenMale => 'open',
            self::WorkingFemale, self::WorkingMale => 'working',
            self::ChampionFemale, self::ChampionMale => 'champion',
            self::VeteranFemale, self::VeteranMale => 'veteran',
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
