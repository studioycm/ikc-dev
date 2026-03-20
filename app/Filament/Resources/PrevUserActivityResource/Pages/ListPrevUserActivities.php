<?php

namespace App\Filament\Resources\PrevUserActivityResource\Pages;

use App\Filament\Resources\PrevUserActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevUserActivities extends ListRecords
{
    protected static string $resource = PrevUserActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
