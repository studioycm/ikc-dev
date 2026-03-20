<?php

namespace App\Filament\Resources\PrevUserTaskResource\Pages;

use App\Filament\Resources\PrevUserTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevUserTasks extends ListRecords
{
    protected static string $resource = PrevUserTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
