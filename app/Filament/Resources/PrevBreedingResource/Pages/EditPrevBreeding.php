<?php

namespace App\Filament\Resources\PrevBreedingResource\Pages;

use App\Filament\Resources\PrevBreedingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevBreeding extends EditRecord
{
    protected static string $resource = PrevBreedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
