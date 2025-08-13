<?php

namespace App\Policies;

use App\Models\PrevJudge;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevJudgePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev judges.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::judge');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PrevJudge $prevJudge): bool
    {
        return $user->can('view_prev::judge');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::judge');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PrevJudge $prevJudge): bool
    {
        return $user->can('update_prev::judge');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PrevJudge $prevJudge): bool
    {
        return $user->can('delete_prev::judge');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_prev::judge');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PrevJudge $prevJudge): bool
    {
        return $user->can('force_delete_prev::judge');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_prev::judge');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PrevJudge $prevJudge): bool
    {
        return $user->can('restore_prev::judge');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_prev::judge');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, PrevJudge $prevJudge): bool
    {
        return $user->can('replicate_prev::judge');
    }

    /**
     * Determine whether the user can reorder models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_prev::judge');
    }
}
