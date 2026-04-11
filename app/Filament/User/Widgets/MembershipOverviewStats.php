<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\MembershipsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevClubUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MembershipOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $prevUserId = $this->getCurrentPrevUserId();

        $membershipsQuery = PrevClubUser::query()
            ->when(
                blank($prevUserId),
                fn($query) => $query->whereRaw('1 = 0'),
                function ($query) use ($prevUserId) {
                    $query->where('user_id', $prevUserId);
                }
            );

        return [
            Stat::make(__('Active memberships'), (clone $membershipsQuery)
                ->whereNull('deleted_at')
                ->where('expire_date', '>=', now())
                ->where('payment_status', '1')
                ->count())
                ->color('success')
                ->url(MembershipsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Expiring soon'), (clone $membershipsQuery)
                ->whereNull('deleted_at')
                ->whereBetween('expire_date', [now(), now()->copy()->addDays(60)])
                ->count())
                ->color('warning')
                ->url(MembershipsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Expired'), (clone $membershipsQuery)
                ->whereNull('deleted_at')
                ->where('expire_date', '<', now())
                ->count())
                ->color('danger')
                ->url(MembershipsDashboard::getUrl(panel: 'user')),
        ];
    }
}
