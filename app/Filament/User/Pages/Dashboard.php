<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\BillingOverviewStats;
use App\Filament\User\Widgets\BreedingOverviewStats;
use App\Filament\User\Widgets\DogsOverviewStats;
use App\Filament\User\Widgets\MembershipOverviewStats;
use App\Filament\User\Widgets\RequestsOverviewStats;
use App\Filament\User\Widgets\ShowsOverviewStats;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    public static function getRoutePath(): string
    {
        return '/';
    }

    public static function getNavigationLabel(): string
    {
        return __('Dashboard');
    }

    public function getTitle(): string
    {
        return __('Dashboard');
    }

    public function getColumns(): int|string|array
    {
        return [
            'md' => 4,
            'xl' => 9,
        ];
    }

    public function getWidgets(): array
    {
        return [
            DogsOverviewStats::class,
            MembershipOverviewStats::class,
            BreedingOverviewStats::class,
            ShowsOverviewStats::class,
            BillingOverviewStats::class,
            RequestsOverviewStats::class,
        ];
    }
}
