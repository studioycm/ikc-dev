<?php

namespace App\Filament\Resources\PrevVetAuthResource\Pages;

use App\Filament\Resources\PrevVetAuthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevVetAuths extends ListRecords
{
    protected static string $resource = PrevVetAuthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
