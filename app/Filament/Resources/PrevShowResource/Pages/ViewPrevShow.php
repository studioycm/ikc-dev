<?php

namespace App\Filament\Resources\PrevShowResource\Pages;

use App\Filament\Resources\PrevShowResource;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\ViewRecord;

class ViewPrevShow extends ViewRecord
{
    use HasRelationManagers;

    protected static string $resource = PrevShowResource::class;

    protected static bool $hasRelationManagers = true;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return __('Show');
    }
}
