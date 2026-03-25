<?php

namespace App\Filament\User\Widgets\Concerns;

use App\Models\PrevUser;

trait InteractsWithCurrentPrevUser
{
    protected function getCurrentPrevUser(): ?PrevUser
    {
        return auth()->user()?->prevUser;
    }

    protected function getCurrentPrevUserId(): ?int
    {
        return $this->getCurrentPrevUser()?->getKey();
    }

    protected function getCurrentPrevUserPhone(): ?int
    {
        return $this->getCurrentPrevUser()?->normalised_phone;
    }

    /**
     * @return array<int>
     */
    protected function getCurrentUserBreedIds(): array
    {
        return $this->getCurrentPrevUser()?->dogs()
            ->with('breed:id,BreedCode')
            ->get()
            ->pluck('breed.id')
            ->unique()
            ->filter()
            ->values()
            ->all() ?? [];
    }

    /**
     * @return array<int>
     */
    protected function getCurrentPrevUserDogSagirIds(): array
    {
        $prevUser = $this->getCurrentPrevUser();

        if ($prevUser === null) {
            return [];
        }

        $prevUser->loadMissing('dogs:SagirID', 'history_dogs:SagirID');

        return $prevUser->dogs
            ->pluck('SagirID')
            ->merge($prevUser->history_dogs->pluck('SagirID'))
            ->filter()
            ->map(static fn(mixed $sagirId): int => (int)$sagirId)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<int>
     */
    protected function getCurrentPrevUserBreedingHouseIds(): array
    {
        $prevUser = $this->getCurrentPrevUser();

        if ($prevUser === null) {
            return [];
        }

        $prevUser->loadMissing('prevBreedingHouses:id');

        return $prevUser->prevBreedingHouses
            ->pluck('id')
            ->filter()
            ->map(static fn(mixed $id): int => (int)$id)
            ->unique()
            ->values()
            ->all();
    }
}
