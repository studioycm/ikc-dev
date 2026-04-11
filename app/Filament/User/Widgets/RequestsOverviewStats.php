<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\RequestsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevUserRequest;
use App\Services\Legacy\PrevUserService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RequestsOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 1;

    public function getColumns(): int
    {
        return 1;
    }


    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $prevUser = $this->getCurrentPrevUser();
        $prevUserId = $prevUser?->getKey();
        $requestsQuery = app(PrevUserService::class)
            ->constrainRequestQueryToPrevUser(PrevUserRequest::query(), $prevUser);

        return [
            Stat::make(__('Open requests'), (clone $requestsQuery)
                ->where(function ($query): void {
                    $query->where('status', '!=', 'Payment done')
                        ->orWhere('IsDone', '!=', 1);
                })
                ->count())
                ->color('warning')
                ->url(RequestsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Requests'), (clone $requestsQuery)
                ->count())
                ->color('warning')
                ->url(RequestsDashboard::getUrl(panel: 'user')),
        ];
    }
}
