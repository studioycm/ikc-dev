<?php

namespace App\Filament\Resources\PrevPriceResource\Pages;

use App\Filament\Resources\PrevPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevPrice extends ViewRecord
{
    protected static string $resource = PrevPriceResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('Price') . ': ' . $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
