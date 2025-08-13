<?php

namespace App\Filament\Resources\PrevShowPaymentResource\Pages;

use App\Filament\Resources\PrevShowPaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrevShowPayments extends ListRecords
{
    protected static string $resource = PrevShowPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
