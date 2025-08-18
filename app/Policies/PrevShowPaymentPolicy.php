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
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show');
    }

    /**
     * Determine whether the user can view the prev show payment.
     */
    public function view(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('view_prev::show');
    }

    /**
     * Determine whether the user can create prev show payments.
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show');
    }

    /**
     * Determine whether the user can update the prev show payment.
     */
    public function update(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('update_prev::show');
    }

    /**
     * Determine whether the user can delete the prev show payment.
     */
    public function delete(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('delete_prev::show');
    }

    /**
     * Determine whether the user can restore the prev show payment.
     */
    public function restore(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('restore_prev::show');
    }

    /**
     * Determine whether the user can permanently delete the prev show payment.
     */
    public function forceDelete(User $user, PrevShowPayment $prevShowPayment): bool
    {
        return $user->can('force_delete_prev::show');
    }
}
