<?php

namespace App\Observers;

use App\Models\PrevBreed;
use App\Models\PrevClub;
use Illuminate\Support\Facades\DB;

class PrevBreedObserver
{
    protected function clearClubsForBreed(?PrevBreed $breed): void
    {
        if (! $breed) {
            return;
        }

        $connection = $breed->getConnectionName();

        // breed id in BreedsDB is $breed->id
        $clubIds = DB::connection($connection)
            ->table('breed_club')
            ->where('breed_id', $breed->id)
            ->whereNull('deleted_at')
            ->pluck('club_id')
            ->unique()
            ->filter()
            ->values()
            ->all();

        if (! empty($clubIds)) {
            PrevClub::clearCountsCacheForClubs($clubIds);
        }
    }

    public function updated(PrevBreed $breed): void
    {
        $this->clearClubsForBreed($breed);
    }

    public function deleted(PrevBreed $breed): void
    {
        $this->clearClubsForBreed($breed);
    }

    public function restored(PrevBreed $breed): void
    {
        $this->clearClubsForBreed($breed);
    }

    public function forceDeleted(PrevBreed $breed): void
    {
        $this->clearClubsForBreed($breed);
    }
}
