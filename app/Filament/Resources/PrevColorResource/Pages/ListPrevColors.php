<?php

namespace App\Filament\Resources\PrevColorResource\Pages;

use App\Filament\Resources\PrevColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevColors extends ListRecords
{
    protected static string $resource = PrevColorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
