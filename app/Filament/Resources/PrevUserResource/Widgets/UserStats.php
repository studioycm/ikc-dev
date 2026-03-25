<?php

namespace App\Filament\Resources\PrevUserResource\Widgets;

use App\Filament\Resources\PrevUserResource\Pages\ListPrevUsers;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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
            Stat::make(__('Native Users'), $this->getPageTableQuery()->whereNative()->count()),
            Stat::make(__('Owners'), $this->getPageTableQuery()->whereOwners()->count()),
            Stat::make(__('Members'), $this->getPageTableQuery()->whereMembers()->count()),
        ];
    }
}
