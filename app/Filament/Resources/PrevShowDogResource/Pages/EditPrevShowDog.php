<?php

namespace App\Filament\Resources\PrevShowDogResource\Pages;

use App\Filament\Resources\PrevShowDogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowDog extends EditRecord
{
    protected static string $resource = PrevShowDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
