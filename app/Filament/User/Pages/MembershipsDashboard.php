<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\ClubManagersWidget;
use App\Filament\User\Widgets\Sections\UserClubMembershipsTable;
use Filament\Pages\Dashboard as BaseDashboard;

class MembershipsDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $routePath = 'memberships';

    public static function getNavigationLabel(): string
    {
        return __('Membership');
    }

    public function getTitle(): string
    {
        return __('Membership');
    }

    public function getColumns(): int|string|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            UserClubMembershipsTable::class,
            ClubManagersWidget::class,
        ];
    }
}
