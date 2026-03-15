<?php

namespace App\Filament\Resources\PrevUserRequestResource\Pages;

use App\Filament\Resources\PrevUserRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevUserRequests extends ListRecords
{
    protected static string $resource = PrevUserRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
