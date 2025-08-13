<?php

namespace App\Filament\Resources\PrevShowArenaResource\Pages;

use App\Filament\Resources\PrevShowArenaResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowArena extends CreateRecord
{
    protected static string $resource = PrevShowArenaResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
