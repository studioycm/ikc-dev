<?php

namespace App\Policies;

use App\Models\PrevShow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev shows.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show');
    }

    /**
     * Determine whether the user can view the prev show.
     */
    public function view(User $user, PrevShow $prevShow): bool
    {
        return $user->can('view_prev::show');
    }

    /**
     * Determine whether the user can create prev shows.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show');
    }

    /**
     * Determine whether the user can update the prev show.
     */
    public function update(User $user, PrevShow $prevShow): bool
    {
        return $user->can('update_prev::show');
    }

    /**
     * Determine whether the user can delete the prev show.
     */
    public function delete(User $user, PrevShow $prevShow): bool
    {
        return $user->can('delete_prev::show');
    }

    /**
     * Determine whether the user can bulk delete prev shows.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_prev::show');
    }

    /**
     * Determine whether the user can restore the prev show.
     */
    public function restore(User $user, PrevShow $prevShow): bool
    {
        return $user->can('restore_prev::show');
    }

    /**
     * Determine whether the user can bulk restore prev shows.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_prev::show');
    }

    /**
     * Determine whether the user can permanently delete the prev show.
     */
    public function forceDelete(User $user, PrevShow $prevShow): bool
    {
        return $user->can('force_delete_prev::show');
    }

    /**
     * Determine whether the user can permanently bulk delete prev shows.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_prev::show');
    }

    /**
     * Determine whether the user can replicate the prev show.
     */
    public function replicate(User $user, PrevShow $prevShow): bool
    {
        return $user->can('replicate_prev::show');
    }

    /**
     * Determine whether the user can reorder prev shows.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_prev::show');
    }
}
