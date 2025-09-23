<?php

namespace App\Filament\Resources\PrevHealthResource\Pages;

use App\Filament\Resources\PrevHealthResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevHealth extends EditRecord
{
    protected static string $resource = PrevHealthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
