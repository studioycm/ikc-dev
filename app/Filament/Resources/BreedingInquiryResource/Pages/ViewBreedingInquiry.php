<?php

namespace App\Filament\Resources\BreedingInquiryResource\Pages;

use App\Filament\Resources\BreedingInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBreedingInquiry extends ViewRecord
{
    protected static string $resource = BreedingInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
