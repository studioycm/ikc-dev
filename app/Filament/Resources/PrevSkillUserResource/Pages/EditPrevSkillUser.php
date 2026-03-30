<?php

namespace App\Filament\Resources\PrevSkillUserResource\Pages;

use App\Filament\Resources\PrevSkillUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevSkillUser extends EditRecord
{
    protected static string $resource = PrevSkillUserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Edit user skill:') . ' #' . $this->getRecord()->id;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
