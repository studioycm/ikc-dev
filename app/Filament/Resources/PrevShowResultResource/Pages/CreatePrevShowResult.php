<?php

namespace App\Filament\Resources\PrevShowResultResource\Pages;

use App\Filament\Resources\PrevShowResultResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevShowResult extends CreateRecord
{
    protected static string $resource = PrevShowResultResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
