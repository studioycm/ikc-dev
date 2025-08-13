<?php

namespace App\Policies;

use App\Models\PrevShowClass;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowClassPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev show classes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show_class');
    }

    /**
     * Determine whether the user can view the prev show class.
     */
    public function view(User $user, PrevShowClass $prevShowClass): bool
    {
        return $user->can('view_prev::show_class');
    }

    /**
     * Determine whether the user can create prev show classes.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show_class');
    }

    /**
     * Determine whether the user can update the prev show class.
     */
    public function update(User $user, PrevShowClass $prevShowClass): bool
    {
        return $user->can('update_prev::show_class');
    }

    /**
     * Determine whether the user can delete the prev show class.
     */
    public function delete(User $user, PrevShowClass $prevShowClass): bool
    {
        return $user->can('delete_prev::show_class');
    }

    /**
     * Determine whether the user can bulk delete prev show classes.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_prev::show_class');
    }

    /**
     * Determine whether the user can restore the prev show class.
     */
    public function restore(User $user, PrevShowClass $prevShowClass): bool
    {
        return $user->can('restore_prev::show_class');
    }

    /**
     * Determine whether the user can bulk restore prev show classes.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_prev::show_class');
    }

    /**
     * Determine whether the user can permanently delete the prev show class.
     */
    public function forceDelete(User $user, PrevShowClass $prevShowClass): bool
    {
        return $user->can('force_delete_prev::show_class');
    }

    /**
     * Determine whether the user can permanently bulk delete prev show classes.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_prev::show_class');
    }

    /**
     * Determine whether the user can replicate the prev show class.
     */
    public function replicate(User $user, PrevShowClass $prevShowClass): bool
    {
        return $user->can('replicate_prev::show_class');
    }

    /**
     * Determine whether the user can reorder prev show classes.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_prev::show_class');
    }
}
