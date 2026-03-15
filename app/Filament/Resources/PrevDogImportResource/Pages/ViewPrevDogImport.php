<?php

namespace App\Filament\Resources\PrevDogImportResource\Pages;

use App\Filament\Resources\PrevDogImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevDogImport extends ViewRecord
{
    protected static string $resource = PrevDogImportResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('Imported Dog') . ': ' . ($this->record->dog_name ?: '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
