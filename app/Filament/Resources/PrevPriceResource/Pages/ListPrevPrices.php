<?php

namespace App\Filament\Resources\PrevPriceResource\Pages;

use App\Filament\Resources\PrevPriceResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListPrevPrices extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
