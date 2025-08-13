<?php

namespace App\Filament\Resources\PrevShowBreedResource\Pages;

use App\Filament\Resources\PrevShowBreedResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPrevShowBreed extends EditRecord
{
    protected static string $resource = PrevShowBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
