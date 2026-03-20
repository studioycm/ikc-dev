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
        $topic_label = $this->getRecord()->topic->getLabel();
        return __('Displaying') . " " . __('User Request') . ": " . ($topic_label ?? '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
