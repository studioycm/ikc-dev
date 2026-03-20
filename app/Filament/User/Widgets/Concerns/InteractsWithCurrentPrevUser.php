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
}
