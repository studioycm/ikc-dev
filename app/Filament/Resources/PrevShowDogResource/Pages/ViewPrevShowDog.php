<?php

namespace App\Filament\Resources\PrevShowDogResource\Pages;

use App\Filament\Resources\PrevShowDogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevShowDog extends ViewRecord
{
    protected static string $resource = PrevShowDogResource::class;

    public function getTitle(): string
    {
        return __('Displaying') . " " . __('dog/model/general.labels.singular') . ': ' . $this->record->dogName;
    }
}
