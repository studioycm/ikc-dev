<?php

namespace App\Filament\User\Pages;

use App\Filament\User\Widgets\Sections\PaymentHistoryTable;
use Filament\Pages\Dashboard as BaseDashboard;

class PaymentsDashboard extends BaseDashboard
{
    protected static ?int $navigationSort = 80;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static string $routePath = 'payments';

    public static function getNavigationLabel(): string
    {
        return __('Payments');
    }

    public function getTitle(): string
    {
        return __('Payments');
    }

    public function getColumns(): int|string|array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            PaymentHistoryTable::class,
        ];
    }
}
