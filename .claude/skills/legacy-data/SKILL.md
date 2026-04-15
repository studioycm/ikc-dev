---
name: legacy-data
description: "Use this skill whenever working with Prev* models, the mysql_prev database connection, legacy services (LegacyMembershipResolverService, PrevClubMembershipResolverService, PedigreeTreeBuilderService, BreedingDogCheckService, PrevDogBreedingRightsService), or any Filament/Livewire screen that touches legacy dog, user, club, breeding, or show data. Covers: non-standard owner-key joins, decimal-as-code columns, dual ownership semantics, cast/enum/accessor patterns, Shield + policy authorization, and upgrade/migration risks. Do not use for purely modern-app concerns (User model, Spatie roles, Redis, Horizon)."
license: MIT
metadata:
  author: studioycm
---

# Legacy Data Guidance

## When to activate this skill

Activate this skill any time you are:

- Reading or writing `Prev*` models or `mysql_prev` queries
- Working on Filament resources/pages/widgets that touch legacy data
- Working on Livewire components under `app/Livewire/Legacy`
- Working on legacy domain services under `app/Services/Legacy`
- Planning a migration or upgrade that involves legacy tables, casts, enums, or Shield permissions

## Reference files

Read the relevant file before making changes:

- `references/architecture.md` — app split, domain groupings, panels, builders/casts/enums/observers, policies, upgrade
  risks and migration sequence
- `references/legacy-data.md` — full table/model/column mapping, owner-key rules, decimal-as-code rules, all
  `mysql_prev` tables inventory
- `references/filament-shield.md` — Filament resource families, Livewire coupling, Blade files, Shield + policy layer,
  upgrade checklist

## Critical rules (always apply, no exceptions)

### 1. Never infer joins from column names

The legacy schema does **not** use `id -> *_id` conventions throughout. Always verify the join via the model relation
before writing a query.

Key non-standard joins already in production:

| Column                               | Joins to                          |
|--------------------------------------|-----------------------------------|
| `DogsDB.RaceID`                      | `BreedsDB.BreedCode`              |
| `DogsDB.ColorID`                     | `ColorsDB.OldCode`                |
| `DogsDB.HairID`                      | `HairsDB.OldCode`                 |
| `DogsDB.CurrentOwnerId`              | `users.owner_code`                |
| `DogsDB.FatherSAGIR` / `MotherSAGIR` | `DogsDB.SagirID`                  |
| `DogsDB.BeitGidulID`                 | `breedinghouses.GidulCode`        |
| `Shows_Breeds.RaceID`                | `BreedsDB.BreedCode`              |
| `shows_results` (arena)              | uses `MainArenaID`, not `ArenaID` |

### 2. Treat decimal columns as code fields

Many `mysql_prev` columns are stored as `decimal(28,8)` but act as integer-like business codes.
Examples: `SagirID`, `RaceID`, `ColorID`, `HairID`, `CurrentOwnerId`, `BreedCode`, `OldCode`, `ClubCode`, `GidulCode`.

Never cast these to true decimals for arithmetic. The app normalizes them through casts or accessors.

### 3. `PrevUser.id` and `PrevUser.owner_code` are different identifiers

- `PrevUser.id` — row PK; used in pivot tables and most direct relations
- `PrevUser.owner_code` — legacy business owner key; used by `PrevDog::legacyOwner()`

Confusing them produces wrong owner results silently.

### 4. `PrevDog` has dual ownership semantics

- **Current ownership:** `dogs2users` pivot (`SagirID` + `user_id`)
- **Legacy ownership:** `CurrentOwnerId -> owner_code`

Any change to ownership logic must preserve both paths or explicitly define a cutover rule.

### 5. Never remove casts, observers, or accessors without replacing their side effects

- `app/Casts/Legacy/*` normalize legacy decimal/string encodings into booleans or enums
- `app/Observers/PrevBreedObserver.php` and `PrevDogObserver.php` maintain normalization on write
- Model accessors such as `full_name`, `gender_label`, `size_label`, `formatted_label` shape all UI output

Removing any of these without replacement causes silent data corruption or incorrect UI rendering.

### 6. Shield is not a replacement for policies

Shield provides permission structure and Filament integration. The policies in `app/Policies` encode record-level
authorization. Both must stay aligned — migrate them together, not separately.

### 7. Check for direct `mysql_prev` queries in resources and services

Not all legacy data access goes through Eloquent. `PrevShowResource` and other areas use `DB::connection('mysql_prev')`
directly. Always inspect both the model layer and the resource/service layer.

## Common pitfalls

- Writing a migration or schema change assuming `id` is the join key in `mysql_prev`
- Replacing a model accessor with a raw column reference in a Filament column definition
- Porting a Filament resource without checking whether a Livewire component or service adapter is also involved
- Assuming `auth()->user()->prevUser` is always present — it is a hard runtime dependency in the user panel
- Migrating Shield permissions without also checking that the matching policy returns consistent results
- Running `php artisan prev:tables` — it is currently broken; use Boost `database-schema` on `mysql_prev` instead
