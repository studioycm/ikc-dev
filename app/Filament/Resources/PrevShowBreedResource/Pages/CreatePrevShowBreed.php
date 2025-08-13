<?php

namespace App\Filament\Resources\PrevShowBreedResource\Pages;

use App\Filament\Resources\PrevShowBreedResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowBreed extends CreateRecord
{
    protected static string $resource = PrevShowBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
