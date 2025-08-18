<?php

namespace App\Policies;

use App\Models\PrevShowBreed;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowBreedPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev show breeds.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show');
    }

    /**
     * Determine whether the user can view the prev show breed.
     */
    public function view(User $user, PrevShowBreed $prevShowBreed): bool
    {
        return $user->can('view_prev::show');
    }

    /**
     * Determine whether the user can create prev show breeds.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show');
    }

    /**
     * Determine whether the user can update the prev show breed.
     */
    public function update(User $user, PrevShowBreed $prevShowBreed): bool
    {
        return $user->can('update_prev::show');
    }

    /**
     * Determine whether the user can delete the prev show breed.
     */
    public function delete(User $user, PrevShowBreed $prevShowBreed): bool
    {
        return $user->can('delete_prev::show');
    }

    /**
     * Determine whether the user can restore the prev show breed.
     */
    public function restore(User $user, PrevShowBreed $prevShowBreed): bool
    {
        return $user->can('restore_prev::show');
    }

    /**
     * Determine whether the user can permanently delete the prev show breed.
     */
    public function forceDelete(User $user, PrevShowBreed $prevShowBreed): bool
    {
        return $user->can('force_delete_prev::show');
    }
}
