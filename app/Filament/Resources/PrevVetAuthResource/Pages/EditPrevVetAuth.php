<?php

namespace App\Filament\Resources\PrevVetAuthResource\Pages;

use App\Filament\Resources\PrevVetAuthResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevVetAuth extends EditRecord
{
    protected static string $resource = PrevVetAuthResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
