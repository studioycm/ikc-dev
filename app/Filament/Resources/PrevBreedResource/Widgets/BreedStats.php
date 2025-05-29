<?php

namespace App\Filament\Resources\PrevBreedResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\PrevBreedResource\Pages\ListPrevBreeds;

class BreedStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListPrevBreeds::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total'), $this->getPageTableQuery()->count()),
        ];
    }
}
