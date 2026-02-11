<?php

namespace App\Filament\Resources\PrevBreedingRelatedDogResource\Pages;

use App\Filament\Resources\PrevBreedingRelatedDogResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevBreedingRelatedDog extends EditRecord
{
    protected static string $resource = PrevBreedingRelatedDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
