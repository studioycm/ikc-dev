<?php

namespace App\Filament\Resources\PrevBreedResource\Pages;

use App\Filament\Resources\PrevBreedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPrevBreeds extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevBreedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PrevBreedResource\Widgets\BreedStats::class,
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }
}
