<?php

namespace App\Filament\Resources\BreedingInquiryResource\Pages;

use App\Filament\Resources\BreedingInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBreedingInquiry extends EditRecord
{
    protected static string $resource = BreedingInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
