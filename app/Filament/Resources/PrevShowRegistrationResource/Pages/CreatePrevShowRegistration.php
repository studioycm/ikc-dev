<?php

namespace App\Filament\Resources\PrevShowRegistrationResource\Pages;

use App\Filament\Resources\PrevShowRegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowRegistration extends CreateRecord
{
    protected static string $resource = PrevShowRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
