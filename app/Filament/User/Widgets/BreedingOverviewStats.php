<?php

namespace App\Filament\User\Widgets;

use App\Filament\User\Pages\BreedingActivityDashboard;
use App\Filament\User\Widgets\Concerns\InteractsWithCurrentPrevUser;
use App\Models\PrevBreeding;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class BreedingOverviewStats extends BaseWidget
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
        $dogSagirIds = $this->getCurrentPrevUserDogSagirIds();
        $breedingHouseIds = $this->getCurrentPrevUserBreedingHouseIds();

        $breedingQuery = PrevBreeding::query()
            ->when(
                $dogSagirIds === [] && $breedingHouseIds === [],
                fn(Builder $query): Builder => $query->whereRaw('1 = 0'),
                function (Builder $query) use ($breedingHouseIds, $dogSagirIds): Builder {
                    return $query->where(function (Builder $breedingQuery) use ($breedingHouseIds, $dogSagirIds): void {
                        if ($dogSagirIds !== []) {
                            $breedingQuery->whereIn('SagirId', $dogSagirIds)
                                ->orWhereIn('MaleSagirId', $dogSagirIds);
                        }

                        if ($breedingHouseIds !== []) {
                            $method = $dogSagirIds !== [] ? 'orWhereIn' : 'whereIn';
                            $breedingQuery->{$method}('breeding_house_id', $breedingHouseIds);
                        }
                    });
                },
            );

        return [
            Stat::make(__('Breeding Count'), (clone $breedingQuery)->count())
                ->icon('heroicon-o-heart')
                ->url(BreedingActivityDashboard::getUrl(panel: 'user')),
            Stat::make(__('Recent breedings'), (clone $breedingQuery)
                ->whereDate('BreddingDate', '>=', now()->subMonths(12)->toDateString())
                ->count())
                ->color('warning')
                ->url(BreedingActivityDashboard::getUrl(panel: 'user')),
            Stat::make(__('Litters'), (clone $breedingQuery)
                ->whereNotNull('birthing_date')
                ->count())
                ->color('success')
                ->url(BreedingActivityDashboard::getUrl(panel: 'user')),
            Stat::make(__('Puppies recorded'), (string)((clone $breedingQuery)->sum('live_male_puppie') + (clone $breedingQuery)->sum('live_female_puppie')))
                ->color('info')
                ->icon('heroicon-o-sparkles')
                ->url(BreedingActivityDashboard::getUrl(panel: 'user')),
        ];
    }
}
