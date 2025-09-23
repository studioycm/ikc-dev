<?php

namespace App\Filament\Resources\PrevDogDocumentResource\Pages;

use App\Filament\Resources\PrevDogDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevDogDocuments extends ListRecords
{
    protected static string $resource = PrevDogDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
