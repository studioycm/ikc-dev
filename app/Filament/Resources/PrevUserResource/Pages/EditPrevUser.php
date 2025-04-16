<?php

namespace App\Filament\Resources\PrevUserResource\Pages;

use App\Filament\Resources\PrevUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrevUser extends EditRecord
{
    protected static string $resource = PrevUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
