<?php

namespace App\Filament\Resources\PrevPaymentResource\Pages;

use App\Filament\Resources\PrevPaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevPayment extends CreateRecord
{
    protected static string $resource = PrevPaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
