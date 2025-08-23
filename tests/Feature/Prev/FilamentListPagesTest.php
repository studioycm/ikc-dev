<?php

use App\Filament\Resources\PrevShowArenaResource\Pages\ListPrevShowArenas;
use App\Filament\Resources\PrevShowBreedResource\Pages\ListPrevShowBreeds;
use App\Filament\Resources\PrevShowClassResource\Pages\ListPrevShowClasses;
use App\Filament\Resources\PrevShowDogResource\Pages\ListPrevShowDogs;
use App\Filament\Resources\PrevShowResultResource\Pages\ListPrevShowResults;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use function Pest\Livewire\livewire;

/**
 * Filament list page rendering tests for legacy Prev* resources.
 * These ensure we can actually render pages (not just build URLs),
 * by authenticating into the Filament admin panel.
 */
function ensureSuperAdminUser(): User
{
    // Ensure the "super_admin" role exists for the web guard.
    $role = Role::findOrCreate('super_admin', 'web');

    // Create a verified user and grant the role.
    $user = User::factory()->create([
        'email_verified_at' => now(),
        // Unique email to avoid collisions; password not needed for actingAs.
        'email' => 'test+' . Str::random(8) . '@example.com',
    ]);

    $user->assignRole($role);

    return $user;
}

beforeEach(function () {
    // Resolve the Filament admin panel instance (adjust the id if yours differs).
    $panel = Filament::getPanel('admin');
    expect($panel)->not->toBeNull();

    Filament::setCurrentPanel($panel);

    // Authenticate as a verified super admin user on the same guard the panel uses.
    $user = ensureSuperAdminUser();
    $this->actingAs($user, 'web');
    Filament::auth()->login($user);
});

it('renders Prev Show Arenas list page', function () {
    livewire(ListPrevShowArenas::class)->assertOk();
});

it('renders Prev Show Breeds list page', function () {
    livewire(ListPrevShowBreeds::class)->assertOk();
});

it('renders Prev Show Classes list page', function () {
    livewire(ListPrevShowClasses::class)->assertOk();
});

it('renders Prev Show Dogs list page', function () {
    livewire(ListPrevShowDogs::class)->assertOk();
});

it('renders Prev Show Results list page', function () {
    livewire(ListPrevShowResults::class)->assertOk();
});
