<?php

namespace App\Filament\Resources\PrevShowPaymentResource\Pages;

use App\Filament\Resources\PrevShowPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowPayment extends EditRecord
{
    protected static string $resource = PrevShowPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
