<?php

namespace App\Filament\Resources\PrevShowArenaResource\Pages;

use App\Filament\Resources\PrevShowArenaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowArenas extends ListRecords
{
    protected static string $resource = PrevShowArenaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
