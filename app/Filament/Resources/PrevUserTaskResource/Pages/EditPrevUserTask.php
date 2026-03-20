<?php

namespace App\Filament\Resources\PrevUserTaskResource\Pages;

use App\Filament\Resources\PrevUserTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevUserTask extends EditRecord
{
    protected static string $resource = PrevUserTaskResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Edit task:') . ' ' . ($this->getRecord()->task_name ?: '#' . $this->getRecord()->id);
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
