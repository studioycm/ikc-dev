<?php

use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

function ensureSuperAdminUserForUserResourceHttp(): User
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
    '/admin/users',
    '/admin/users/create',
];

it('redirects unauthenticated users to login when visiting user resource pages', function (string $path) {
    $this->get($path)->assertRedirect();
})->with($paths);

it('allows authenticated super admin to access user resource index and create pages', function (string $path) {
    $this->actingAs(ensureSuperAdminUserForUserResourceHttp());

    $this->get($path)->assertOk();
})->with($paths);

it('allows authenticated super admin to access user edit page (legacy select included)', function () {
    $this->actingAs(ensureSuperAdminUserForUserResourceHttp());

    $target = User::factory()->create([
        'email' => 'target+' . Str::random(8) . '@example.com',
    ]);

    $this->get('/admin/users/' . $target->getKey() . '/edit')->assertOk();
});
