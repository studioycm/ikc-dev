<?php

namespace App\Filament\Resources\PrevTitleResource\Pages;

use App\Filament\Resources\PrevTitleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevTitles extends ListRecords
{
    protected static string $resource = PrevTitleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
