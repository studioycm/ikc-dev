<?php

namespace App\Filament\Resources\PrevUserRequestResource\Pages;

use App\Filament\Resources\PrevUserRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevUserRequest extends CreateRecord
{
    protected static string $resource = PrevUserRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
