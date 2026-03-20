<?php

namespace App\Filament\Resources\PrevClubResource\Pages;

use App\Filament\Resources\PrevClubResource;
use Filament\Resources\Pages\EditRecord;

class EditPrevClub extends EditRecord
{
    protected static string $resource = PrevClubResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
