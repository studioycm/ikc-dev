<?php

namespace App\Filament\Resources\PrevUserResource\Pages;

use App\Filament\Resources\PrevUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevUsers extends ListRecords
{
    protected static string $resource = PrevUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
