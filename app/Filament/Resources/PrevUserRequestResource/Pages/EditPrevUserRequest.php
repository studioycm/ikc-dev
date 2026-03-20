<?php

namespace App\Filament\Resources\PrevUserRequestResource\Pages;

use App\Filament\Resources\PrevUserRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditPrevUserRequest extends EditRecord
{
    protected static string $resource = PrevUserRequestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getHeading(): string|Htmlable
    {
        $topic_label = $this->getRecord()->topic->getLabel();
        return __('Edit request:') . ' ' . ($topic_label ?? '#' . $this->getRecord()->id);
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
