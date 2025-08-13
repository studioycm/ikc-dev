<?php

namespace App\Filament\Resources\PrevShowArenaResource\Pages;

use App\Filament\Resources\PrevShowArenaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowArena extends EditRecord
{
    protected static string $resource = PrevShowArenaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
