<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Filament\Resources\PrevDogResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevDog extends CreateRecord
{
    protected static string $resource = PrevDogResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
