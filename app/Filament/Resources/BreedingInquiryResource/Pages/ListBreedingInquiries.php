<?php

namespace App\Filament\Resources\BreedingInquiryResource\Pages;

use App\Filament\Resources\BreedingInquiryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBreedingInquiries extends ListRecords
{
    protected static string $resource = BreedingInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
