<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Filament\Resources\PrevDogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevDogs extends ListRecords
{
    protected static string $resource = PrevDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
