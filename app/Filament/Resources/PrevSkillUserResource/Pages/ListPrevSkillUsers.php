<?php

namespace App\Filament\Resources\PrevSkillUserResource\Pages;

use App\Filament\Resources\PrevSkillUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrevSkillUsers extends ListRecords
{
    protected static string $resource = PrevSkillUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
