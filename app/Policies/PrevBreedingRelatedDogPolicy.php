<?php

namespace App\Policies;

use App\Models\PrevBreedingRelatedDog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevBreedingRelatedDogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any breeding related dogs.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::breeding::related::dog');
    }

    /**
     * Determine whether the user can view the breeding related dog.
     *
     * @param User $user
     * @param PrevBreedingRelatedDog $breedingRelatedDog
     * @return bool
     */
    public function view(User $user, PrevBreedingRelatedDog $breedingRelatedDog): bool
    {
        return $user->can('view_prev::breeding::related::dog');
    }

    /**
     * Determine whether the user can create breeding related dogs.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::breeding::related::dog');
    }

    /**
     * Determine whether the user can update the breeding related dog.
     *
     * @param User $user
     * @param PrevBreedingRelatedDog $breedingRelatedDog
     * @return bool
     */
    public function update(User $user, PrevBreedingRelatedDog $breedingRelatedDog): bool
    {
        return $user->can('update_prev::breeding::related::dog');
    }

    /**
     * Determine whether the user can delete the breeding related dog.
     *
     * @param User $user
     * @param PrevBreedingRelatedDog $breedingRelatedDog
     * @return bool
     */
    public function delete(User $user, PrevBreedingRelatedDog $breedingRelatedDog): bool
    {
        return $user->can('delete_prev::breeding::related::dog');
    }

    /**
     * Determine whether the user can restore the breeding related dog.
     *
     * @param User $user
     * @param PrevBreedingRelatedDog $breedingRelatedDog
     * @return bool
     */
    public function restore(User $user, PrevBreedingRelatedDog $breedingRelatedDog): bool
    {
        return $user->can('restore_prev::breeding::related::dog');
    }

    /**
     * Determine whether the user can permanently delete the breeding related dog.
     *
     * @param User $user
     * @param PrevBreedingRelatedDog $breedingRelatedDog
     * @return bool
     */
    public function forceDelete(User $user, PrevBreedingRelatedDog $breedingRelatedDog): bool
    {
        return $user->can('force_delete_prev::breeding::related::dog');
    }
}
