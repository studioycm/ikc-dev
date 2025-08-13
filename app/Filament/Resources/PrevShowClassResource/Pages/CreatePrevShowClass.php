<?php

namespace App\Filament\Resources\PrevShowClassResource\Pages;

use App\Filament\Resources\PrevShowClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowClass extends CreateRecord
{
    protected static string $resource = PrevShowClassResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
