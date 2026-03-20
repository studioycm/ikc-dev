<?php

namespace App\Filament\User\Resources\BreedingInquiryResource\Pages;

use App\Filament\User\Resources\BreedingInquiryResource;
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
