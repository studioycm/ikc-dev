<?php

namespace App\Filament\Resources\PrevDogResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\PrevDogResource\Pages\ListPrevDogs;

class DogStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListPrevDogs::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total'), $this->getPageTableQuery()->count()),
        ];
    }
}
