<?php

namespace App\Filament\Resources\PrevShowResultResource\Pages;

use App\Filament\Resources\PrevShowResultResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowResult extends EditRecord
{
    protected static string $resource = PrevShowResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
