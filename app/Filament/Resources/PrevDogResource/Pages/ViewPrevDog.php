<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Filament\Resources\PrevDogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevDog extends ViewRecord
{
    protected static string $resource = PrevDogResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('General');
    }
}
