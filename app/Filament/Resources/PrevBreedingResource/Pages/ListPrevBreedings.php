<?php

namespace App\Filament\Resources\PrevBreedingResource\Pages;

use App\Filament\Resources\PrevBreedingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevBreedings extends ListRecords
{
    protected static string $resource = PrevBreedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
