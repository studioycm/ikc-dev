<?php

namespace App\Filament\Resources\PrevShowRegistrationResource\Pages;

use App\Filament\Resources\PrevShowRegistrationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowRegistrations extends ListRecords
{
    protected static string $resource = PrevShowRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
