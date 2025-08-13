<?php

namespace App\Filament\Resources\PrevShowDogResource\Pages;

use App\Filament\Resources\PrevShowDogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowDogs extends ListRecords
{
    protected static string $resource = PrevShowDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
