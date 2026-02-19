<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $routePath = '/';

    public function getColumns(): int|string|array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            Widgets\UserDogsTableWidget::class,
            Widgets\ClubMembershipsWidget::class,
            Widgets\ClubManagersWidget::class,
        ];
    }
}
