<?php

namespace App\Filament\Resources\PrevUserResource\Pages;

use App\Filament\Resources\PrevUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPrevUsers extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PrevUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PrevUserResource\Widgets\UserStats::class,
        ];
    }

    public function setPage($page, $pageName = 'page'): void
    {
        parent::setPage($page, $pageName);

        $this->dispatch('scroll-to-top');
    }
}
