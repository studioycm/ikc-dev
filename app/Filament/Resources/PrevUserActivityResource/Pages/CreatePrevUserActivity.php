<?php

namespace App\Filament\Resources\PrevUserActivityResource\Pages;

use App\Filament\Resources\PrevUserActivityResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevUserActivity extends CreateRecord
{
    protected static string $resource = PrevUserActivityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
