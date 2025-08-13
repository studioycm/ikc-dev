<?php

namespace App\Filament\Resources\PrevShowResource\Pages;

use App\Filament\Resources\PrevShowResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShow extends EditRecord
{
    protected static string $resource = PrevShowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
