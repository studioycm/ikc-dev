<?php

namespace App\Filament\Resources\PrevUserTaskResource\Pages;

use App\Filament\Resources\PrevUserTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevUserTask extends ViewRecord
{
    protected static string $resource = PrevUserTaskResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('User Task') . ': ' . ($this->record->task_name ?: '#' . $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
