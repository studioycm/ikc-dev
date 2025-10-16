<?php

namespace App\Observers;

use App\Models\PrevClub;
use App\Models\PrevDog;
use Illuminate\Support\Facades\DB;

class PrevDogObserver
{
    /**
     * Handle events where a dog was created/updated/deleted/restored/forceDeleted.
     * We compute affected club IDs and clear their cached counts.
     */
    protected function clearAffectedClubs(?PrevDog $dog): void
    {
        static::clearAffectedClubsForDog($dog);
    }

    /**
     * Public helper to clear affected clubs for a given dog.
     * Useful when model events are intentionally suppressed (e.g., within a table lock),
     * allowing callers to perform the cache clearing afterwards.
     */
    public static function clearAffectedClubsForDog(?PrevDog $dog): void
    {
        if (! $dog) {
            return;
        }

        $connection = $dog->getConnectionName();

        // If RaceID is null, nothing to do
        $raceId = $dog->RaceID ?? null;
        if ($raceId === null) {
            return;
        }

        // Find breed(s) that have BreedCode == RaceID, then get club ids in breed_club
        $clubIds = DB::connection($connection)
            ->table('BreedsDB as b')
            ->select('bc.club_id')
            ->join('breed_club as bc', 'bc.breed_id', '=', 'b.id')
            ->where('b.BreedCode', $raceId)
            ->whereNull('bc.deleted_at')
            ->pluck('club_id')
            ->unique()
            ->filter()
            ->values()
            ->all();

        if (! empty($clubIds)) {
            PrevClub::clearCountsCacheForClubs($clubIds);
        }
    }

    public function created(PrevDog $dog): void
    {
        $this->clearAffectedClubs($dog);
    }

    public function updated(PrevDog $dog): void
    {
        $this->clearAffectedClubs($dog);
    }

    public function deleted(PrevDog $dog): void
    {
        $this->clearAffectedClubs($dog);
    }

    public function restored(PrevDog $dog): void
    {
        $this->clearAffectedClubs($dog);
    }

    public function forceDeleted(PrevDog $dog): void
    {
        $this->clearAffectedClubs($dog);
    }
}
