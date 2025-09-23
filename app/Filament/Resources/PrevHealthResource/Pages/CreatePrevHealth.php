<?php

namespace App\Filament\Resources\PrevHealthResource\Pages;

use App\Filament\Resources\PrevHealthResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevHealth extends CreateRecord
{
    protected static string $resource = PrevHealthResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
