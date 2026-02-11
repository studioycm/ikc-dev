<?php

namespace App\Filament\Resources\PrevBreedingRelatedDogResource\Pages;

use App\Filament\Resources\PrevBreedingRelatedDogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevBreedingRelatedDog extends CreateRecord
{
    protected static string $resource = PrevBreedingRelatedDogResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
