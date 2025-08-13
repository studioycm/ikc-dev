<?php

namespace App\Policies;

use App\Models\PrevJudge;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevJudgePolicy{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev judges.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the prev judge.
     *
     * @param User $user
     * @param PrevJudge $prevJudge
     * @return bool
     */
    public function view(User $user, PrevJudge $prevJudge): bool
    {
    }

    /**
     * Determine whether the user can create prev judges.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
    }

    /**
     * Determine whether the user can update the prev judge.
     *
     * @param User $user
     * @param PrevJudge $prevJudge
     * @return bool
     */
    public function update(User $user, PrevJudge $prevJudge): bool
    {
    }

    /**
     * Determine whether the user can delete the prev judge.
     *
     * @param User $user
     * @param PrevJudge $prevJudge
     * @return bool
     */
    public function delete(User $user, PrevJudge $prevJudge): bool
    {
    }

    /**
     * Determine whether the user can restore the prev judge.
     *
     * @param User $user
     * @param PrevJudge $prevJudge
     * @return bool
     */
    public function restore(User $user, PrevJudge $prevJudge): bool
    {
    }

    /**
     * Determine whether the user can permanently delete the prev judge.
     *
     * @param User $user
     * @param PrevJudge $prevJudge
     * @return bool
     */
    public function forceDelete(User $user, PrevJudge $prevJudge): bool
    {
    }
}
