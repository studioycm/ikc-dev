<?php

namespace App\Filament\Resources\PrevShowClassResource\Pages;

use App\Filament\Resources\PrevShowClassResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowClass extends EditRecord
{
    protected static string $resource = PrevShowClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
