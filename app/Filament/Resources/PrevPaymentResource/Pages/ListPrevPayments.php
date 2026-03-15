<?php

namespace App\Filament\Resources\PrevPaymentResource\Pages;

use App\Filament\Resources\PrevPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevPayments extends ListRecords
{
    protected static string $resource = PrevPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
