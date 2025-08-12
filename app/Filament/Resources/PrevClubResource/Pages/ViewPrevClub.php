<?php

namespace App\Filament\Resources\PrevClubResource\Pages;

use App\Filament\Resources\PrevClubResource;
use App\Models\PrevClub;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevClub extends ViewRecord
{
    protected static string $resource = PrevClubResource::class;

    // Let Filament handle the layout and page rendering.
    protected static string $view = 'filament.resources.prev-club.view';

    protected function getViewData(): array
    {
        $club = $this->getRecord();
        // Ensure type for clarity; Filament resolves the record already.
        \assert($club instanceof PrevClub);

        return [
            'record'   => $club,
            'infolist' => PrevClubResource::getInfolistForRecord($club),
            'resource' => static::getResource(),
        ];
    }
}
