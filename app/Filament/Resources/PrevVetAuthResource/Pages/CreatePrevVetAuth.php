<?php

namespace App\Filament\Resources\PrevVetAuthResource\Pages;

use App\Filament\Resources\PrevVetAuthResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevVetAuth extends CreateRecord
{
    protected static string $resource = PrevVetAuthResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
