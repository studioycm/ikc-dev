<?php

namespace App\Policies;

use App\Models\PrevShowPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev show payments.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show_payment');
    }

    /**
     * Determine whether the user can view the prev show payment.
     *
     * @param User $user
     * @param PrevShowPayment $prevShowPayment
     * @return bool
     */
    public function view(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('view_prev::show_payment');
    }

    /**
     * Determine whether the user can create prev show payments.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show_payment');
    }

    /**
     * Determine whether the user can update the prev show payment.
     *
     * @param User $user
     * @param PrevShowPayment $prevShowPayment
     * @return bool
     */
    public function update(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('update_prev::show_payment');
    }

    /**
     * Determine whether the user can delete the prev show payment.
     *
     * @param User $user
     * @param PrevShowPayment $prevShowPayment
     * @return bool
     */
    public function delete(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('delete_prev::show_payment');
    }

    /**
     * Determine whether the user can restore the prev show payment.
     *
     * @param User $user
     * @param PrevShowPayment $prevShowPayment
     * @return bool
     */
    public function restore(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('restore_prev::show_payment');
    }

    /**
     * Determine whether the user can permanently delete the prev show payment.
     *
     * @param User $user
     * @param PrevShowPayment $prevShowPayment
     * @return bool
     */
    public function forceDelete(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('force_delete_prev::show_payment');
    }
}
