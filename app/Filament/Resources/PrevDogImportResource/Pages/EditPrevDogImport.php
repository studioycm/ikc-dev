<?php

namespace App\Filament\Resources\PrevDogImportResource\Pages;

use App\Filament\Resources\PrevDogImportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevDogImport extends EditRecord
{
    protected static string $resource = PrevDogImportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Edit imported dog:') . ' ' . ($this->getRecord()->dog_name ?: '#' . $this->getRecord()->id);
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
