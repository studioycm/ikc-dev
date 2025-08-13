<?php

namespace App\Filament\Resources\PrevShowBreedResource\Pages;

use App\Filament\Resources\PrevShowBreedResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowBreeds extends ListRecords
{
    protected static string $resource = PrevShowBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
