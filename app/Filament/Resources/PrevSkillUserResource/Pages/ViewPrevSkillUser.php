<?php

namespace App\Filament\Resources\PrevSkillUserResource\Pages;

use App\Filament\Resources\PrevSkillUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevSkillUser extends ViewRecord
{
    protected static string $resource = PrevSkillUserResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . ' ' . __('User Skill') . ': #' . $this->record->id;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
