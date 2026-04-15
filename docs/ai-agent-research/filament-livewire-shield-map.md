# Filament, Livewire, Blade, and Shield Research Map

## Why this layer matters

Most legacy structure problems are hidden from the UI by:

- Filament resource queries and labels
- Livewire components that call legacy domain services
- model accessors / casts / enums
- Shield permissions plus policies

Any UI rewrite that only ports form fields and table columns will miss a large amount of embedded legacy logic.

## Filament panels

### Admin panel

Provider: `app\Providers\Filament\AdminPanelProvider.php`

Characteristics:

- Default admin panel at `/admin`
- Uses Shield plugin
- Protected by role middleware requiring `super_admin|admin`
- Discovers resources, pages, widgets from the main Filament namespace
- Defines many navigation groups, which already encode the project information architecture

### User panel

Provider: `app\Providers\Filament\UserPanelProvider.php`

Characteristics:

- Separate user panel at `/user`
- Uses the same `web` auth guard
- Discovers user resources/pages/widgets under `app\Filament\User\*`
- Renders `prev-user-badge` using `auth()->user()->prevUser`

## Main resource families

### Core admin resources under `app\Filament\Resources`

High-signal families:

- dog domain
    - `PrevDogResource`
    - `PrevBreedResource`
    - `PrevColorResource`
    - `PrevHairResource`
    - `PrevDogDocumentResource`
    - `PrevDogImportResource`
    - `PrevHealthResource`
    - `PrevTitleResource`
- breeding domain
    - `PrevBreedingResource`
    - `PrevBreedingRelatedDogResource`
    - `PrevBreedingHouseResource`
- user/club domain
    - `PrevUserResource`
    - `PrevClubResource`
    - `PrevSkillUserResource`
- shows domain
    - `PrevShowResource`
    - `PrevShowArenaResource`
    - `PrevShowBreedResource`
    - `PrevShowClassResource`
    - `PrevShowDogResource`
    - `PrevShowRegistrationResource`
    - `PrevShowPaymentResource`
    - `PrevShowResultResource`
    - `PrevJudgeResource`
- auth / administration
    - `UserResource`
    - `RoleResource`

### User-panel resources/pages/widgets under `app\Filament\User`

High-signal pages:

- `Dashboard`
- `DogsDashboard`
- `MembershipsDashboard`
- `RequestsDashboard`
- `PaymentsDashboard`
- `BreedingActivityDashboard`
- `ShowsDashboard`

High-signal widgets / sections:

- `UserDogsTableWidget`
- `MembershipOverviewStats`
- `ShowsOverviewStats`
- `Widgets\Sections\UserDogsTable`
- `Widgets\Sections\UserClubMembershipsTable`
- `Widgets\Sections\PaymentHistoryTable`
- `Widgets\Sections\ShowParticipationTable`
- `Widgets\Sections\BreedingActivityTable`
- `Widgets\Sections\UserRequestsTable`
- `Widgets\Concerns\InteractsWithCurrentPrevUser`

The user panel is especially dependent on the `User -> PrevUser` bridge and should be treated as legacy-aware UI, not as
purely modern app UI.

## Relation managers

Relation managers are important because they often reveal which side of a relationship is treated as canonical in the
UI.

Examples to review before any rewrite:

- `app\Filament\Resources\PrevDogResource\RelationManagers\MaleBreedingsRelationManager.php`
- `app\Filament\Resources\PrevDogResource\RelationManagers\FemaleBreedingsRelationManager.php`
- show-related relation managers under `app\Filament\Resources\PrevShowResource\RelationManagers`

When relation managers work, that usually means hidden owner-key logic is already correct at the model layer. Breaking
the
model relation will break the manager even if the table renders still look fine.

## Widgets, exporters, importers

### Widgets

Widgets exist in both:

- `app\Filament\Widgets`
- `app\Filament\User\Widgets`

They should be reviewed for:

- direct queries
- eager-loading assumptions
- `PrevUser`-scoped filtering
- stats built from legacy relation accessors rather than raw columns

### Exporters / importers

There are Filament exporter/importer classes in the Filament tree and they are migration-sensitive because export/import
code tends to flatten legacy relation logic into column maps.

Any upgrade should check for:

- whether exported labels come from accessors or raw columns
- whether imported fields map to `id` keys or business-code owner keys
- whether decimal legacy codes are normalized before persistence or lookup

## Example of hidden legacy logic in a resource

### `PrevShowResource`

File: `app\Filament\Resources\PrevShowResource.php`

Important observation:

- the `has_results` filter directly queries `DB::connection('mysql_prev')->table('shows_results')`
- the UI filter then compares the resulting ids against `ShowsDB.id`

This means the resource is not just a passive CRUD screen. It contains legacy reporting logic.

### `PrevDogResource` family

The dog resource and its relation managers depend on `PrevDog` accessors and special joins, especially:

- `breed()` via `BreedCode`
- `color()` via `OldCode`
- `hair()` via `OldCode`
- owner resolution via both `dogs2users` and `CurrentOwnerId -> owner_code`
- pedigree self-joins via `SagirID`

## Livewire components

Confirmed component set under `app\Livewire`:

- `app\Livewire\Legacy\Breeding\ClubMembershipCompact.php`
- `app\Livewire\Legacy\Breeding\DogChecksTable.php`
- `app\Livewire\Legacy\Breeding\MembershipTypeBadges.php`
- `app\Livewire\Legacy\Pedigree\DogSummary.php`
- `app\Livewire\Legacy\Pedigree\ParentsPairForm.php`
- `app\Livewire\Legacy\Pedigree\PedigreeTree.php`
- `app\Livewire\Legacy\Pedigree\PedigreeTreeRegular.php`
- `app\Livewire\Prev\PrevClub\PrevClubBreedsTable.php`

### Important component/service coupling

- `DogChecksTable` depends on `App\Services\Legacy\Breeding\BreedingDogCheckService`
- pedigree components depend on legacy dog/pedigree services and self-referential `SagirID` logic
- membership components depend on membership resolver services and legacy club pricing / payment status rules

This means Livewire in this app is a domain adapter, not merely a rendering layer.

## Blade files and Blade components

Confirmed relevant Blade files:

- `resources\views\filament\components\loading-indicator.blade.php`
- `resources\views\filament\infolists\components\prev-dog-pedigree-tree.blade.php`
- `resources\views\filament\user\components\prev-user-badge.blade.php`
- `resources\views\livewire\legacy\breeding\club-membership-compact.blade.php`
- `resources\views\livewire\legacy\breeding\club-membership-details-modal.blade.php`
- `resources\views\livewire\legacy\breeding\dog-check-details-modal.blade.php`
- `resources\views\livewire\legacy\breeding\dog-checks-table.blade.php`
- `resources\views\livewire\legacy\breeding\membership-type-badges.blade.php`
- `resources\views\livewire\legacy\pedigree\dog-summary.blade.php`
- `resources\views\livewire\legacy\pedigree\parents-pair-form.blade.php`
- `resources\views\livewire\legacy\pedigree\pedigree-tree.blade.php`
- `resources\views\livewire\legacy\pedigree\pedigree-tree-regular.blade.php`
- `resources\views\livewire\legacy\pedigree\partials\pedigree-certificate-header.blade.php`
- `resources\views\livewire\legacy\pedigree\partials\pedigree-certificate-node.blade.php`
- `resources\views\livewire\resources\prev-club\prev-club-breeds-table.blade.php`

Key implication:

- visual labels, RTL/LTR behavior, and final end-user naming are distributed across both PHP and Blade.

## Shield + policies

### Shield integration points

Confirmed code locations:

- `app\Providers\Filament\AdminPanelProvider.php`
- `app\Filament\Resources\RoleResource.php`
- `app\Models\User.php`

Key behaviors:

- `User` uses Shield integration traits
- `RoleResource` implements Shield-specific interfaces/traits and defines permission prefixes
- the admin panel loads the Shield plugin directly

### Policy layer

The app also keeps standard Laravel policies for legacy resources, including:

- `PrevBreedPolicy`
- `PrevBreedingPolicy`
- `PrevClubPolicy`
- `PrevDogPolicy`
- `PrevShowPolicy`
- `PrevUserPolicy`
- many more under `app\Policies`

Important migration rule:

> Shield is not a replacement for the policies in this codebase.

Shield provides permission structure and Filament integration; policies still encode resource authorization rules.

## Upgrade checklist for UI and auth layers

Before rebuilding any panel or resource, verify all of the following:

1. Which model accessor / cast / enum is currently shaping the displayed label?
2. Which relation uses a business code instead of `id`?
3. Is the screen using a service layer instead of pure model reads?
4. Is the resource using direct `mysql_prev` queries?
5. Does the screen assume `auth()->user()->prevUser` exists?
6. Is access controlled by Shield, policies, or both?
7. Does a widget or relation manager rely on eager-loaded legacy relations?
