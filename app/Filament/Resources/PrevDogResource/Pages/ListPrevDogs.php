<?php

namespace App\Filament\Resources\PrevDogResource\Pages;

use App\Filament\Resources\PrevDogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPrevDogs extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevDogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PrevDogResource\Widgets\DogStats::class,
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);
 
        $this->dispatch('scroll-to-top');
    }
}
