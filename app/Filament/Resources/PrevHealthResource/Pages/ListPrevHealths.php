<?php

namespace App\Filament\Resources\PrevHealthResource\Pages;

use App\Filament\Resources\PrevHealthResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevHealths extends ListRecords
{
    protected static string $resource = PrevHealthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
