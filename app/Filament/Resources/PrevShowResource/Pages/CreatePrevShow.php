<?php

namespace App\Filament\Resources\PrevShowResource\Pages;

use App\Filament\Resources\PrevShowResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShow extends CreateRecord
{
    protected static string $resource = PrevShowResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
