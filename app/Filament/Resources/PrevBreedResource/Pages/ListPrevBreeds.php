<?php

namespace App\Filament\Resources\PrevBreedResource\Pages;

use App\Filament\Resources\PrevBreedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevBreeds extends ListRecords
{
    protected static string $resource = PrevBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
