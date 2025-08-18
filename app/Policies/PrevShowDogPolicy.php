<?php

namespace App\Policies;

use App\Models\PrevShowDog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowDogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any show dogs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show');
    }

    /**
     * Determine whether the user can view the show dog.
     */
    public function view(User $user, PrevShowDog $prevShowDog): bool
    {
        return $user->can('view_prev::show');
    }

    /**
     * Determine whether the user can create show dogs.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show');
    }

    /**
     * Determine whether the user can update the show dog.
     */
    public function update(User $user, PrevShowDog $prevShowDog): bool
    {
        return $user->can('update_prev::show');
    }

    /**
     * Determine whether the user can delete the show dog.
     */
    public function delete(User $user, PrevShowDog $prevShowDog): bool
    {
        return $user->can('delete_prev::show');
    }

    /**
     * Determine whether the user can restore the show dog.
     */
    public function restore(User $user, PrevShowDog $prevShowDog): bool
    {
        return $user->can('restore_prev::show');
    }

    /**
     * Determine whether the user can permanently delete the show dog.
     */
    public function forceDelete(User $user, PrevShowDog $prevShowDog): bool
    {
        return $user->can('force_delete_prev::show');
    }
}
