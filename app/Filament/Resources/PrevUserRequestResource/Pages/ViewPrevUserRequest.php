<?php

namespace App\Filament\Resources\PrevUserRequestResource\Pages;

use App\Filament\Resources\PrevUserRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevUserRequest extends ViewRecord
{
    protected static string $resource = PrevUserRequestResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('User Request') . ': ' . ($this->record->topic ?: '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
