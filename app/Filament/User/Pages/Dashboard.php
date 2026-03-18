<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\BillingOverviewStats;
use App\Filament\User\Widgets\DogsOverviewStats;
use App\Filament\User\Widgets\MembershipOverviewStats;
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
            'md' => 2,
            'xl' => 3,
        ];
    }

    public function getWidgets(): array
    {
        return [
            DogsOverviewStats::class,
            MembershipOverviewStats::class,
            BillingOverviewStats::class,
        ];
    }
}
