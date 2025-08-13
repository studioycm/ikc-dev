<?php

namespace App\Filament\Resources\PrevShowDogResource\Pages;

use App\Filament\Resources\PrevShowDogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowDog extends CreateRecord
{
    protected static string $resource = PrevShowDogResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
