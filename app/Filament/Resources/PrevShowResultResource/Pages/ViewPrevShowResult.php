<?php

namespace App\Filament\Resources\PrevShowResultResource\Pages;

use App\Filament\Resources\PrevShowResultResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevShowResult extends ViewRecord
{
    protected static string $resource = PrevShowResultResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . " " . __('Result') . ': ' . $this->record->resultDog->full_name . " | " . $this->record->show->TitleName;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

}
