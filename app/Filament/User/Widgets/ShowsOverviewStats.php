<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\ShowsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevShowDog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class ShowsOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $dogSagirIds = $this->getCurrentPrevUserDogSagirIds();

        $showEntriesQuery = PrevShowDog::query()
            ->when(
                $dogSagirIds === [],
                fn(Builder $query): Builder => $query->whereRaw('1 = 0'),
                fn(Builder $query): Builder => $query->whereIn('SagirID', $dogSagirIds),
            );

        return [
            Stat::make(__('Show entries'), (clone $showEntriesQuery)->count())
                ->icon('heroicon-o-ticket')
                ->url(ShowsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Shows attended'), (clone $showEntriesQuery)->distinct('ShowID')->count('ShowID'))
                ->color('info')
                ->url(ShowsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Results received'), (clone $showEntriesQuery)->has('prevShowResult')->count())
                ->color('success')
                ->url(ShowsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Upcoming entries'), (clone $showEntriesQuery)
                ->whereHas('show', fn(Builder $query): Builder => $query->whereDate('StartDate', '>=', now()->toDateString()))
                ->count())
                ->color('warning')
                ->url(ShowsDashboard::getUrl(panel: 'user')),
        ];
    }
}
