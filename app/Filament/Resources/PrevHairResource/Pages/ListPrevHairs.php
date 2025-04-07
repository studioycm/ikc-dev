<?php

namespace App\Filament\Resources\PrevHairResource\Pages;

use App\Filament\Resources\PrevHairResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevHairs extends ListRecords
{
    protected static string $resource = PrevHairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
