<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\PaymentsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BillingOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 2;

    protected function getColumns(): int
    {
        return 2;
    }

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $prevUser = $this->getCurrentPrevUser();
        $prevUserId = $prevUser?->getKey();
        $paymentsQuery = PrevPayment::query()
            ->when(
                blank($prevUserId),
                fn($query) => $query->whereRaw('1 = 0'),
                function ($query) use ($prevUserId) {
                    $query->where('created_by', '=', $prevUserId);
                },
            );

        return [
            Stat::make(__('Payments'), (clone $paymentsQuery)
                ->count())
                ->color('success')
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Payments this year'), (clone $paymentsQuery)
                ->whereYear('payment_date_time', now()->year)
                ->count())
                ->color('success')
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Paid this year'), number_format((float)((clone $paymentsQuery)
                ->whereYear('payment_date_time', now()->year)
                ->sum('amount')), 0))
                ->icon('heroicon-o-banknotes')
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Paid Total'), number_format((float)((clone $paymentsQuery)
                ->sum('amount')), 0))
                ->icon('heroicon-o-banknotes')
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
        ];
    }
}
