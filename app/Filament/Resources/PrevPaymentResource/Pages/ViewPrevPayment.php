<?php

namespace App\Filament\Resources\PrevPaymentResource\Pages;

use App\Filament\Resources\PrevPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevPayment extends ViewRecord
{
    protected static string $resource = PrevPaymentResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('Payment') . ': ' . ($this->record->approval_number ?: '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
