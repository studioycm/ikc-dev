<?php

namespace App\Filament\Resources\PrevShowResultResource\Pages;

use App\Filament\Resources\PrevShowResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowResults extends ListRecords
{
    protected static string $resource = PrevShowResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
