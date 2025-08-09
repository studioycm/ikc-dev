<?php

namespace App\Filament\Resources\PrevClubResource\Pages;

use App\Filament\Resources\PrevClubResource;
use App\Models\PrevClub;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewPrevClub extends ViewRecord
{
    protected static string $resource = PrevClubResource::class;

    public function render(): View
    {
        // Ensure we pass a fully-hydrated model instance to the resource builder.
        $club = PrevClub::findOrFail((int) $this->record->id);

        $infolist = PrevClubResource::getInfolistForRecord($club);

        return view('filament.resources.prev-club.view', [
            'record'   => $club,
            'infolist' => $infolist,
            'resource' => static::getResource(),
        ]);
    }
}
