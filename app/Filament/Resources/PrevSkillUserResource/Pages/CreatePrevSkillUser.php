<?php

namespace App\Filament\Resources\PrevSkillUserResource\Pages;

use App\Filament\Resources\PrevSkillUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePrevSkillUser extends CreateRecord
{
    protected static string $resource = PrevSkillUserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
