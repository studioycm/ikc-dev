<?php

namespace App\Filament\Resources\PrevShowClassResource\Pages;

use App\Filament\Resources\PrevShowClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowClasses extends ListRecords
{
    protected static string $resource = PrevShowClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
