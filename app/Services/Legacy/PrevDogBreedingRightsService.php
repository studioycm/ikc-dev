<?php

namespace App\Services\Legacy;

use App\Models\PrevUser;

class PrevDogBreedingRightsService
{
    public function getFemaleCoOwners(int $femaleId, int $authPrevUserId): PrevUser
    {
        // All owners of the female dog except current user
        return PrevUser::whereHas('prevDogs', fn($q) => $q->where('id', $femaleId))
            ->where('id', '<>', $authPrevUserId)
            ->get();
    }

    public function getMaleOwners(int $maleId): PrevUser
    {
        return PrevUser::whereHas('prevDogs', fn($q) => $q->where('id', $maleId))
            ->get();
    }
}
