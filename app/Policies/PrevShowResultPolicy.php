<?php

namespace App\Policies;

use App\Models\PrevShowResult;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowResultPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev show results.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show');
    }

    /**
     * Determine whether the user can view the prev show result.
     */
    public function view(User $user, PrevShowResult $prevShowResult): bool
    {
        return $user->can('view_prev::show');
    }

    /**
     * Determine whether the user can create prev show results.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show');
    }

    /**
     * Determine whether the user can update the prev show result.
     */
    public function update(User $user, PrevShowResult $prevShowResult): bool
    {
        return $user->can('update_prev::show');
    }

    /**
     * Determine whether the user can delete the prev show result.
     */
    public function delete(User $user, PrevShowResult $prevShowResult): bool
    {
        return $user->can('delete_prev::show');
    }

    /**
     * Determine whether the user can restore the prev show result.
     */
    public function restore(User $user, PrevShowResult $prevShowResult): bool
    {
        return $user->can('restore_prev::show');
    }

    /**
     * Determine whether the user can permanently delete the prev show result.
     */
    public function forceDelete(User $user, PrevShowResult $prevShowResult): bool
    {
        return $user->can('force_delete_prev::show');
    }
}
