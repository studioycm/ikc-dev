<?php

use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Basic HTTP tests to ensure we can render pages after building URLs.
 * - Unauthenticated users are redirected to the admin login.
 * - Authenticated super admin users receive HTTP 200 on index pages.
 */
function ensureSuperAdminUserHttp(): User
{
    $role = Role::findOrCreate('super_admin', 'web');

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'email' => 'http+' . Str::random(8) . '@example.com',
    ]);

    $user->assignRole($role);

    return $user;
}

$paths = [
    '/admin/prev-show-arenas',
    '/admin/prev-show-breeds',
    '/admin/prev-show-classes',
    '/admin/prev-show-dogs',
    '/admin/prev-show-results',
];

it('redirects unauthenticated users to login when visiting admin prev pages', function (string $path) {
    $this->get($path)->assertRedirect();
})->with($paths);

it('allows authenticated super admin to access admin prev pages', function (string $path) {
    $this->actingAs(ensureSuperAdminUserHttp());

    $this->get($path)->assertOk();
})->with($paths);
