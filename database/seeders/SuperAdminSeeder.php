<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Seeds a Super Admin user for development / non-production.
 * - Creates the user if missing.
 * - Always sets the known non-production password to guarantee access.
 * - Ensures the super admin role exists and assigns it to the user.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'ycm@data4.work';
        $password = 'y0n1@1kc5y5';

        // Create or get the user by email.
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
            ],
        );

        // Always ensure the password is set to the expected non-production value.
        $user->forceFill([
            'password' => Hash::make($password),
        ])->save();

        // Resolve role name and guard from Shield config if available.
        $roleName = config('filament-shield.super_admin.name', 'super_admin');
        $guard = config('filament-shield.guard', 'web');

        // Ensure the role exists.
        $role = Role::findOrCreate($roleName, $guard);

        // Assign the role (idempotent).
        if (!$user->hasRole($role->name)) {
            $user->assignRole($role->name);
        }
    }
}
