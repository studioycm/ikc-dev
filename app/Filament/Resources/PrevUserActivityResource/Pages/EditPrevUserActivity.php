<?php

namespace App\Filament\Resources\PrevUserActivityResource\Pages;

use App\Filament\Resources\PrevUserActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevUserActivity extends EditRecord
{
    protected static string $resource = PrevUserActivityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getHeading(): string|Htmlable
    {
        return __('Edit activity:') . ' ' . ($this->getRecord()->Activity_Type ?: '#' . $this->getRecord()->id);
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
