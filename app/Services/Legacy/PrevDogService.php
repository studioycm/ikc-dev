<?php

namespace App\Services\Legacy;

use App\Enums\Legacy\LegacyDogGender;
use App\Enums\Legacy\LegacySagirPrefix;
use App\Models\PrevDog;
use App\Observers\PrevDogObserver;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PrevDogService
{
    /**
     * Create a new minimal PrevDog record with automatically assigned SagirID and DataID.
     * This method performs a short critical section using a table WRITE lock on DogsDB to
     * prevent "max+1" races in the legacy database, since migrations/sequence tables are not available.
     *
     * Notes:
     * - Uses the mysql_prev connection implicitly via PrevDog's $connection.
     * - Applies defaults per project rules (RaceID from inheritFrom if missing, ColorID=9000, HairID=4,
     *   sagir_prefix=NUL, RegDate=today, Gender by argument).
     * - Uniqueness for Chip/DnaID should be enforced by the calling Filament form validation.
     */
    public function createMinimalParent(array $data, ?LegacyDogGender $gender = null, ?PrevDog $inheritFrom = null): PrevDog
    {
        // Normalize and apply required defaults before persisting.
        if ($gender !== null) {
            $data['GenderID'] = $gender->value;
        }

        if ($inheritFrom !== null) {
            $data['RaceID'] = $data['RaceID'] ?? $inheritFrom->RaceID;
            $data['sagir_prefix'] = $data['sagir_prefix'] ?? ($inheritFrom->sagir_prefix?->value ?? LegacySagirPrefix::NUL->value);
        }

        $data['ColorID'] = $data['ColorID'] ?? 9000;
        $data['HairID'] = $data['HairID'] ?? 4;
        $data['sagir_prefix'] = $data['sagir_prefix'] ?? LegacySagirPrefix::NUL->value;

        // Normalize dates: accept Y-m-d or d-m-Y strings, Carbon instances, or null.
        $birth = $data['BirthDate'] ?? null;
        if ($birth instanceof \DateTimeInterface) {
            $data['BirthDate'] = Carbon::instance($birth);
        } elseif (is_string($birth)) {
            $birth = trim($birth);
            try {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth) === 1) {
                    $data['BirthDate'] = Carbon::createFromFormat('Y-m-d', $birth)->startOfDay();
                } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $birth) === 1) {
                    $data['BirthDate'] = Carbon::createFromFormat('d-m-Y', $birth)->startOfDay();
                } else {
                    $data['BirthDate'] = Carbon::parse($birth);
                }
            } catch (\Throwable) {
                $data['BirthDate'] = null;
            }
        } else {
            $data['BirthDate'] = null;
        }

        $reg = $data['RegDate'] ?? null;
        if ($reg instanceof \DateTimeInterface) {
            $data['RegDate'] = Carbon::instance($reg);
        } elseif (is_string($reg)) {
            $reg = trim($reg);
            try {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $reg) === 1) {
                    $data['RegDate'] = Carbon::createFromFormat('Y-m-d', $reg)->startOfDay();
                } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $reg) === 1) {
                    $data['RegDate'] = Carbon::createFromFormat('d-m-Y', $reg)->startOfDay();
                } else {
                    $data['RegDate'] = Carbon::parse($reg);
                }
            } catch (\Throwable) {
                $data['RegDate'] = null;
            }
        }
        $data['RegDate'] = $data['RegDate'] ?? Carbon::now();

        // Open a dedicated connection handle to ensure lock/unlock is executed on mysql_prev.
        $conn = DB::connection(PrevDog::getModel()->getConnectionName());

        $created = null;

        try {
            // LOCK TABLE to guarantee uniqueness while computing MAX()+1.
            $conn->unprepared('LOCK TABLES `DogsDB` WRITE');

            // Compute next SagirID and DataID while locked.
            $nextSagir = (int)(PrevDog::withTrashed()->max('SagirID') ?? 0) + 1;
            $nextData = (int)(PrevDog::withTrashed()->max('DataID') ?? 0) + 1;

            $data['SagirID'] = $data['SagirID'] ?? $nextSagir;
            $data['DataID'] = $data['DataID'] ?? $nextData;

            // Create the record within the lock window so no other writer can reuse IDs.
            // Suppress model events to avoid observers querying other tables during LOCK TABLES.
            $created = PrevDog::withoutEvents(fn() => PrevDog::create($data));
        } finally {
            // Always unlock even if an exception is thrown.
            try {
                $conn->unprepared('UNLOCK TABLES');
            } catch (\Throwable) {
                // swallow unlock failures to not mask the original exception
            }
        }

        // Now that tables are unlocked, safely clear affected clubs (observer logic).
        PrevDogObserver::clearAffectedClubsForDog($created);

        return $created;
    }
}
