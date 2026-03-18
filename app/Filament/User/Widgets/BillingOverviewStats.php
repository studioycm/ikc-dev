<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\PaymentsDashboard;
use App\Filament\User\Pages\RequestsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevPayment;
use App\Models\PrevUserRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BillingOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 1;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $prevUserId = $this->getCurrentPrevUserId();

        $requestsQuery = PrevUserRequest::query()
            ->when(
                blank($prevUserId),
                fn($query) => $query->whereRaw('1 = 0'),
                fn($query) => $query->where('owner_id', $prevUserId)
            );

        $paymentsQuery = PrevPayment::query()
            ->when(
                blank($prevUserId),
                fn($query) => $query->whereRaw('1 = 0'),
                fn($query) => $query->where('created_by', $prevUserId)
            );

        return [
            Stat::make(__('Open requests'), (clone $requestsQuery)
                ->where(function ($query): void {
                    $query->where('status', '!=', 'Payment done')
                        ->orWhere('IsDone', '!=', 1);
                })
                ->count())
                ->color('warning')
                ->url(RequestsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Payments this year'), (clone $paymentsQuery)
                ->whereYear('payment_date_time', now()->year)
                ->count())
                ->color('success')
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Paid this year'), number_format((float)((clone $paymentsQuery)
                ->whereYear('payment_date_time', now()->year)
                ->sum('amount')), 0))
                ->description(__('ILS'))
                ->icon('heroicon-o-banknotes')
                ->url(PaymentsDashboard::getUrl(panel: 'user')),
        ];
    }
}
