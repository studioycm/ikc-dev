<?php

namespace App\Filament\Resources\PrevShowPaymentResource\Pages;

use App\Filament\Resources\PrevShowPaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowPayment extends CreateRecord
{
    protected static string $resource = PrevShowPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
