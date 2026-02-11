<?php

use App\Enums\Legacy\LegacyDogGender;
use App\Models\PrevBreeding;
use App\Models\PrevDog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

it('computes female breeding stats and age from loaded relations', function () {
    Carbon::setTestNow(Carbon::parse('2024-06-15'));

    $dog = new PrevDog([
        'BirthDate' => Carbon::parse('2020-01-10'),
        'GenderID' => LegacyDogGender::Female,
    ]);

    $femaleBreedingA = new PrevBreeding(['birthing_date' => '2023-05-01']);
    $femaleBreedingB = new PrevBreeding(['birthing_date' => '2024-01-20']);
    $maleBreeding = new PrevBreeding(['birthing_date' => '2024-06-01']);

    $dog->setRelation('femaleBreedings', new Collection([$femaleBreedingA, $femaleBreedingB]));
    $dog->setRelation('maleBreedings', new Collection([$maleBreeding]));

    expect($dog->age_years)->toBe('4y 5m (53m)')
        ->and($dog->female_breedings_count)->toBe(2)
        ->and($dog->male_breedings_count)->toBe(1)
        ->and($dog->last_breeding_date)->toBe('20/01/2024');

    Carbon::setTestNow();
});

it('computes last litter date for male dogs from loaded relations', function () {
    $dog = new PrevDog([
        'GenderID' => LegacyDogGender::Male,
    ]);

    $maleBreedingA = new PrevBreeding(['birthing_date' => '2022-11-05']);
    $maleBreedingB = new PrevBreeding(['birthing_date' => '2023-02-01']);

    $dog->setRelation('maleBreedings', new Collection([$maleBreedingA, $maleBreedingB]));
    $dog->setRelation('femaleBreedings', new Collection);

    expect($dog->last_breeding_date)->toBe('01/02/2023');
});

it('returns null age when birth date is missing', function () {
    $dog = new PrevDog([
        'GenderID' => LegacyDogGender::Female,
    ]);

    expect($dog->age_years)->toBeNull();
});
