<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $routePath = '/';

    public function getColumns(): int|string|array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\User\Widgets\UserDogsTableWidget::class,
            \App\Filament\User\Widgets\ClubMembershipsWidget::class,
            \App\Filament\User\Widgets\ClubManagersWidget::class,
        ];
    }
}
