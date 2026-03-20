<?php

namespace App\Filament\User\Widgets;

use App\Enums\Legacy\LegacyDogGender;
use App\Filament\User\Pages\DogsDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevDog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class DogsOverviewStats extends BaseWidget
{
    use InteractsWithCurrentPrevUser;

    protected int|string|array $columnSpan = 1;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $dogsQuery = PrevDog::query()
            ->whereHas('owners', function (Builder $query): void {
                $query->where('users.id', $this->getCurrentPrevUserId());
            });

        return [
            Stat::make(__('Total dogs'), $dogsQuery->count())
                ->icon('heroicon-o-heart')
                ->url(DogsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Female dogs'), (clone $dogsQuery)->where('GenderID', LegacyDogGender::Female->value)->count())
                ->color('pink')
                ->url(DogsDashboard::getUrl(panel: 'user')),
            Stat::make(__('Male dogs'), (clone $dogsQuery)->where('GenderID', LegacyDogGender::Male->value)->count())
                ->color('info')
                ->url(DogsDashboard::getUrl(panel: 'user')),
        ];
    }
}
