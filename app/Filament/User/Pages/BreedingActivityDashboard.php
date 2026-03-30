<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\BreedingOverviewStats;
use App\Filament\User\Widgets\Sections\BreedingActivityTable;
use Filament\Pages\Dashboard as BaseDashboard;

class BreedingActivityDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static string $routePath = 'breeding-activity';

    public static function getNavigationLabel(): string
    {
        return __('Breeding Activity');
    }

    public function getTitle(): string
    {
        return __('Breeding Activity');
    }

    public function getColumns(): int|string|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            BreedingOverviewStats::class,
            BreedingActivityTable::class,
        ];
    }
}
