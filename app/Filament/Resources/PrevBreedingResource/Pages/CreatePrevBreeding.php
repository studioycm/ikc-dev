<?php

namespace App\Filament\Resources\PrevBreedingResource\Pages;

use App\Filament\Resources\PrevBreedingResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevBreeding extends CreateRecord
{
    protected static string $resource = PrevBreedingResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
