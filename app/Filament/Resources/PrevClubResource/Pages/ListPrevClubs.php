<?php

namespace App\Filament\Resources\PrevClubResource\Pages;

use App\Filament\Resources\PrevClubResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevClubs extends ListRecords
{
    protected static string $resource = PrevClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
