<?php

namespace App\Filament\Resources\PrevTitleResource\Pages;

use App\Filament\Resources\PrevTitleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrevTitle extends EditRecord
{
    protected static string $resource = PrevTitleResource::class;

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
