<?php

namespace App\Filament\Resources\PrevClubResource\Pages;

use App\Filament\Resources\PrevClubResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevClub extends CreateRecord
{
    protected static string $resource = PrevClubResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
