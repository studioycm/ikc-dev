<?php

use App\Filament\Resources\PrevBreedingResource\Pages\ListPrevBreedings;
use App\Filament\Resources\PrevBreedResource\Pages\ListPrevBreeds;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use function Pest\Livewire\livewire;

function ensureSuperAdminUserForLocalizationBatch(): User
{
    $role = Role::findOrCreate('super_admin', 'web');

    $user = User::factory()->create([
        'email_verified_at' => now(),
        'email' => 'locale-admin+' . Str::random(8) . '@example.com',
    ]);

    $user->assignRole($role);

    return $user;
}

beforeEach(function () {
    app()->setLocale('he');

    $panel = Filament::getPanel('admin');

    expect($panel)->not->toBeNull();

    Filament::setCurrentPanel($panel);

    $user = ensureSuperAdminUserForLocalizationBatch();

    $this->actingAs($user, 'web');
    Filament::auth()->login($user);
});

it('renders translated user resource table labels and actions in hebrew', function () {
    $panelUserRole = Role::findOrCreate('panel_user', 'web');

    $record = User::factory()->create([
        'email' => 'localized-user+' . Str::random(8) . '@example.com',
    ]);

    $record->assignRole($panelUserRole);

    livewire(ListUsers::class)
        ->assertTableColumnExists('roles.name', fn(TextColumn $column): bool => $column->getLabel() === __('Role'))
        ->assertTableColumnExists('email_verified_at', fn(IconColumn $column): bool => $column->getLabel() === __('Verified'))
        ->assertTableActionHasLabel('email_verification', __('Verify'), $record)
        ->assertTableActionHasLabel('email_verified', __('Verified'), $record)
        ->assertTableActionHasLabel('send_db_notice', __('Notify'), $record)
        ->assertTableActionHasLabel('send_email', __('Send Email'), $record)
        ->assertTableBulkActionHasLabel('bulk_send_email', __('Send Email'));
});

it('renders translated legacy resource table columns in hebrew', function () {
    livewire(ListPrevBreeds::class)
        ->assertTableColumnExists('id', fn(TextColumn $column): bool => $column->getLabel() === __('ID'))
        ->assertTableColumnExists('BreedCode', fn(TextColumn $column): bool => $column->getLabel() === __('Breed Code'))
        ->assertTableColumnExists('fci_group', fn(TextColumn $column): bool => $column->getLabel() === __('FCI Group'));

    livewire(ListPrevBreedings::class)
        ->assertTableColumnExists('Rules_IsOwner', fn(IconColumn $column): bool => $column->getLabel() === __('Is Owner'))
        ->assertTableColumnExists('Male_More_Than_5', fn(IconColumn $column): bool => $column->getLabel() === __('Male > 5 Litters'))
        ->assertTableColumnExists('less_than_8_years', fn(IconColumn $column): bool => $column->getLabel() === __('< 8 Years'));
});

it('provides the approved hebrew translations for the localization batch', function () {
    expect(__('Search (ID, Name)'))->toBe('חיפוש (מזהה, שם)')
        ->and(__('Subject'))->toBe('נושא')
        ->and(__('Notification created'))->toBe('ההתראה נוצרה')
        ->and(__('Class Name'))->not->toBe('Class Name')
        ->and(__('Arena'))->toBe('זירה')
        ->and(__('common.labels.metadata'))->toBe('נתוני מערכת')
        ->and(__('Comma separated'))->not->toBe('Comma separated');
});

it('does not keep raw user-facing fallback strings in the user dogs table widget', function () {
    $contents = file_get_contents(app_path('Filament/User/Widgets/Sections/UserDogsTable.php'));

    expect($contents)
        ->not->toContain('"ID: {$record->id}"')
        ->not->toContain("?? '~'")
        ->not->toContain("?? 'n/a'");
});
