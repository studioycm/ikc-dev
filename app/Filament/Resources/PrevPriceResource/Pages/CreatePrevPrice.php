<?php

namespace App\Filament\Resources\PrevPriceResource\Pages;

use App\Filament\Resources\PrevPriceResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevPrice extends CreateRecord
{
    protected static string $resource = PrevPriceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
