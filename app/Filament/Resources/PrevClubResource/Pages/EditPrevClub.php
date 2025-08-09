<?php

namespace App\Filament\Resources\PrevClubResource\Pages;

use App\Filament\Resources\PrevClubResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevClub extends EditRecord
{
    protected static string $resource = PrevClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
