<?php

namespace App\Filament\Resources\PrevDogImportResource\Pages;

use App\Filament\Resources\PrevDogImportResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevDogImport extends CreateRecord
{
    protected static string $resource = PrevDogImportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
