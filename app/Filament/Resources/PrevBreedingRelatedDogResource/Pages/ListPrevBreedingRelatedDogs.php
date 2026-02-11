<?php

namespace App\Filament\Resources\PrevBreedingRelatedDogResource\Pages;

use App\Filament\Resources\PrevBreedingRelatedDogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevBreedingRelatedDogs extends ListRecords
{
    protected static string $resource = PrevBreedingRelatedDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
