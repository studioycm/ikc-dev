# IKC App Architecture and Upgrade Map

## Runtime baseline

- Laravel: `12.56.0`
- Filament: `3.3.49`
- Livewire: `3.7.11`
- PHP: `8.4`
- Main DB connection: `mysql`
- Legacy DB connection: `mysql_prev`
- Main auth model: `App\Models\User`
- Legacy bridge model for most business data: `App\Models\PrevUser`

## Core application split

The app is effectively a hybrid system with two business layers:

1. **Modern app layer on `mysql`**
    - `App\Models\User`
    - Spatie roles / permissions + Filament Shield
    - Current auth, admin panel access, and panel routing
2. **Legacy business-data layer on `mysql_prev`**
    - Most `Prev*` models
    - Most dog / show / club / breeding / legacy user data
    - Large amount of domain meaning hidden behind custom owner keys and legacy naming

This means a future Filament v4/v5 migration is not only a UI upgrade. It is also a legacy data-contract preservation
problem.

## Main architectural rule

The most important project-specific rule is:

> Do not infer joins from column names alone.

In this codebase, many legacy relations intentionally do **not** use `id -> *_id` conventions.

Examples already codified in the app:

- `PrevDog.RaceID -> PrevBreed.BreedCode`
- `PrevDog.ColorID -> PrevColor.OldCode`
- `PrevDog.HairID -> PrevHair.OldCode`
- `PrevDog.FatherSAGIR -> PrevDog.SagirID`
- `PrevDog.MotherSAGIR -> PrevDog.SagirID`
- `PrevDog.CurrentOwnerId -> PrevUser.owner_code`
- `PrevShowBreed.RaceID -> PrevBreed.BreedCode`
- Several show-related tables use decimal columns as FK-like values even when their semantic value is an integer code

## Main domain groupings

### Legacy dogs and pedigree

Main files:

- `app\Models\PrevDog.php`
- `app\Models\PrevBreed.php`
- `app\Models\PrevColor.php`
- `app\Models\PrevHair.php`
- `app\Models\PrevTitle.php`
- `app\Models\PrevDogTitle.php`
- `app\Models\PrevDogDocument.php`
- `app\Models\PrevHealth.php`
- `app\Models\PrevDogImport.php`
- `app\Services\Legacy\Pedigree\PedigreeTreeBuilderService.php`
- `app\Livewire\Legacy\Pedigree\*`

This area contains the most upgrade-sensitive owner-key logic.

### Legacy users, memberships, clubs, and permissions bridge

Main files:

- `app\Models\PrevUser.php`
- `app\Builders\PrevUserBuilder.php`
- `app\Models\PrevClub.php`
- `app\Models\PrevClubUser.php`
- `app\Services\Legacy\LegacyMembershipResolverService.php`
- `app\Services\Legacy\PrevClubMembershipResolverService.php`
- `app\Models\User.php`

This is the second most upgrade-sensitive area because panel logic frequently starts with a modern `User` and then
pivots
into legacy `PrevUser` data.

### Legacy breeding workflow

Main files:

- `app\Models\PrevBreeding.php`
- `app\Models\PrevBreedingRelatedDog.php`
- `app\Services\Legacy\PrevDogBreedingRightsService.php`
- `app\Services\Legacy\Breeding\BreedingDogCheckService.php`
- `app\Filament\Resources\PrevBreedingResource.php`
- `app\Filament\Resources\PrevDogResource\RelationManagers\MaleBreedingsRelationManager.php`
- `app\Livewire\Legacy\Breeding\*`

This layer converts legacy dog flags and historical breeding data into UI-friendly approval states.

### Legacy shows management

Main files:

- `app\Models\PrevShow.php`
- `app\Models\PrevShowArena.php`
- `app\Models\PrevShowBreed.php`
- `app\Models\PrevShowClass.php`
- `app\Models\PrevShowDog.php`
- `app\Models\PrevShowRegistration.php`
- `app\Models\PrevShowPayment.php`
- `app\Models\PrevShowResult.php`
- `app\Filament\Resources\PrevShowResource.php`

This area contains both Eloquent mappings and direct `DB::connection('mysql_prev')` queries, so it must be reviewed both
as model design and as query design.

## Panels

### Admin panel

File: `app\Providers\Filament\AdminPanelProvider.php`

Important traits:

- Default Filament panel at `/admin`
- Uses `RoleMiddleware::class . ':super_admin|admin'`
- Uses `FilamentShieldPlugin::make()`
- Discovers resources/pages/widgets from:
    - `app\Filament\Resources`
    - `app\Filament\Pages`
    - `app\Filament\Widgets`
- Large navigation grouping strategy already exists and should be preserved or explicitly redesigned during migration

### User panel

File: `app\Providers\Filament\UserPanelProvider.php`

Important traits:

- Separate panel at `/user`
- Discovers from:
    - `app\Filament\User\Resources`
    - `app\Filament\User\Pages`
    - `app\Filament\User\Widgets`
- Injects a legacy badge via `resources\views\filament\user\components\prev-user-badge.blade.php`
- Assumes `auth()->user()->prevUser` is present and usable

That last assumption is a hard migration dependency.

## Legacy translation / label layer

Labels are not always a direct reflection of DB names.

Where labels are normalized:

- Filament resource labels, navigation labels, column labels, form labels
- enum label methods
- model accessors such as `full_name`, `formatted_label`, `gender_label`, `size_label`
- translations in `lang\he.json` and other translation sources

This matters because any rewrite that uses DB names directly will lose business-facing naming conventions.

## Builders, casts, enums, and observers that hide legacy semantics

### Builder

- `app\Builders\PrevUserBuilder.php`
    - Centralizes search and record-source filtering for legacy users

### Casts

- `app\Casts\DecimalBooleanCast.php`
- `app\Casts\Legacy\LegacyDogGenderCast.php`
- `app\Casts\Legacy\LegacyDogSizeCast.php`
- `app\Casts\Legacy\LegacyDogStatusCast.php`
- `app\Casts\Legacy\LegacyShowTypeCast.php`

These casts are important because several legacy columns are stored as decimals, strings, or mixed legacy encodings
while
the application consumes them as booleans / enums.

### Enums

- `app\Enums\Legacy\LegacyDogGender.php`
- `app\Enums\Legacy\LegacyDogSize.php`
- `app\Enums\Legacy\LegacyDogStatus.php`
- `app\Enums\Legacy\LegacyPedigreeColor.php`
- `app\Enums\Legacy\LegacySagirPrefix.php`
- `app\Enums\Legacy\LegacyShowClass.php`
- `app\Enums\Legacy\LegacyShowTypeEnum.php`
- `app\Enums\Legacy\LegacyUserRequestChampionType.php`
- `app\Enums\Legacy\LegacyUserRequestPaperType.php`
- `app\Enums\Legacy\LegacyUserRequestTopic.php`

### Observers

- `app\Observers\PrevBreedObserver.php`
- `app\Observers\PrevDogObserver.php`

If a rewrite removes observers without replacing their side effects, legacy normalization can silently regress.

## Policies and authorization

Policies exist for almost every important `Prev*` resource under `app\Policies`.

Important implication:

- Shield handles permission generation and role assignment
- Policies still define the actual record-level authorization contracts
- Migration work must keep both systems aligned

High-signal files:

- `app\Policies\RolePolicy.php`
- `app\Policies\UserPolicy.php`
- `app\Policies\PrevDogPolicy.php`
- `app\Policies\PrevShowPolicy.php`
- `app\Policies\PrevBreedingPolicy.php`
- `app\Policies\PrevUserPolicy.php`

## Existing research commands and artifacts

Read-only research commands already exist under `app\Console\Commands\LegacyResearch`.

Confirmed useful commands:

- `php artisan legacy:overview --no-interaction`
- `php artisan legacy:samples --limit=20 --no-interaction`

Generated artifacts confirmed during this research:

- `docs\legacy-data-research\overview.csv`
- `docs\legacy-data-research\overview.md`
- `docs\legacy-data-research\sample_top_shows_by_dogs.csv`
- `docs\legacy-data-research\sample_top_shows_by_payments.csv`
- `docs\legacy-data-research\sample_shows_intersection.csv`
- `docs\legacy-data-research\samples.csv`

## Highest-risk upgrade concerns

1. **Relationship rewrites that assume standard keys**
    - Highest corruption risk
2. **Treating decimal legacy identifiers as true decimals rather than integer-like codes**
    - High join mismatch risk
3. **Replacing model accessors/casts with direct column rendering in Filament v4/v5**
    - High UI correctness risk
4. **Breaking `User -> PrevUser` assumptions in the user panel**
    - High runtime risk
5. **Migrating Shield or policies without comparing permission prefixes and policy behavior**
    - High authorization risk
6. **Porting only Eloquent relations while forgetting direct raw `mysql_prev` queries in resources/services**
    - High functional regression risk

## Recommended migration sequence

1. Freeze and document legacy joins, owner keys, and enum/cast rules.
2. Build parity tests around the highest-value legacy reads.
3. Migrate legacy domain services before redesigning UI resources.
4. Migrate policies and Shield configuration together, not separately.
5. Only then rebuild Filament resources/pages/widgets on top of verified read models.
