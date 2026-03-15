<?php

namespace App\Filament\Resources\PrevDogImportResource\Pages;

use App\Filament\Resources\PrevDogImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevDogImports extends ListRecords
{
    protected static string $resource = PrevDogImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
