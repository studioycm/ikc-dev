<?php

namespace App\Policies;

use App\Models\PrevShowRegistration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrevShowRegistrationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any prev show registrations.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_prev::show_registration');
    }

    /**
     * Determine whether the user can view the prev show registration.
     *
     * @param User $user
     * @param PrevShowRegistration $prevShowRegistration
     * @return bool
     */
    public function view(User $user, PrevShowRegistration $prevShowRegistration): bool
    {
        return $user->can('view_prev::show_registration');
    }

    /**
     * Determine whether the user can create prev show registrations.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_prev::show_registration');
    }

    /**
     * Determine whether the user can update the prev show registration.
     *
     * @param User $user
     * @param PrevShowRegistration $prevShowRegistration
     * @return bool
     */
    public function update(User $user, PrevShowRegistration $prevShowRegistration): bool
    {
        return $user->can('update_prev::show_registration');
    }

    /**
     * Determine whether the user can delete the prev show registration.
     *
     * @param User $user
     * @param PrevShowRegistration $prevShowRegistration
     * @return bool
     */
    public function delete(User $user, PrevShowRegistration $prevShowRegistration): bool
    {
        return $user->can('delete_prev::show_registration');
    }

    /**
     * Determine whether the user can restore the prev show registration.
     *
     * @param User $user
     * @param PrevShowRegistration $prevShowRegistration
     * @return bool
     */
    public function restore(User $user, PrevShowRegistration $prevShowRegistration): bool
    {
        return $user->can('restore_prev::show_registration');
    }

    /**
     * Determine whether the user can permanently delete the prev show registration.
     *
     * @param User $user
     * @param PrevShowRegistration $prevShowRegistration
     * @return bool
     */
    public function forceDelete(User $user, PrevShowRegistration $prevShowRegistration): bool
    {
        return $user->can('force_delete_prev::show_registration');
    }
}
