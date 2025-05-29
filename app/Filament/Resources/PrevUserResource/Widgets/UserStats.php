<?php

namespace App\Filament\Resources\PrevUserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use App\Filament\Resources\PrevUserResource\Pages\ListPrevUsers;

class UserStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ListPrevUsers::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total'), $this->getPageTableQuery()->count()),
            Stat::make(__('Native Users'), $this->getPageTableQuery()->where('record_type', 'Native')->count()),
            Stat::make(__('Owners'), $this->getPageTableQuery()->where('record_type', 'Owners')->count()),
            Stat::make(__('Members'), $this->getPageTableQuery()->where('record_type', 'Members')->count()),
        ];
    }
}
