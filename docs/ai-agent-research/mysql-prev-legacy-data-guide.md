# mysql_prev Legacy Data Guide

## Why this database is dangerous to interpret casually

The `mysql_prev` schema mixes several identity styles:

- normal surrogate keys such as `id`
- business codes such as `SagirID`, `BreedCode`, `OldCode`, `OwnerCode`, `ClubCode`, `GidulCode`
- decimal columns used as integer-like foreign keys
- old/new ownership representations in parallel
- misleading names where `*_id` does not always point to a primary key

For this app, the code is the source of truth for relation semantics. The DB schema alone is not enough.

## Global rules for AI agents and developers

### 1. Do not assume `id` is the owner key

Counterexamples already in production code:

- `DogsDB.RaceID -> BreedsDB.BreedCode`
- `DogsDB.ColorID -> ColorsDB.OldCode`
- `DogsDB.HairID -> HairsDB.OldCode`
- `DogsDB.CurrentOwnerId -> users.owner_code`
- `DogsDB.FatherSAGIR -> DogsDB.SagirID`
- `DogsDB.MotherSAGIR -> DogsDB.SagirID`

### 2. Treat many decimal columns as code fields

Legacy tables commonly use `decimal(28,8)` for values that are effectively integer identifiers.

Examples:

- `DogsDB.SagirID`
- `DogsDB.RaceID`
- `DogsDB.ColorID`
- `DogsDB.HairID`
- `DogsDB.CurrentOwnerId`
- `BreedsDB.BreedCode`
- `ColorsDB.OldCode`
- `HairsDB.OldCode`
- `clubs.ClubCode`
- `breedinghouses.GidulCode`

In practice, the application often casts them to integers or uses enums / accessors so the UI never exposes the raw
storage oddity.

### 3. A legacy column name may describe business meaning, not relational meaning

Examples:

- `owner_id`-style names can refer to `PrevUser.id` in one place and `PrevUser.owner_code` in another
- `RaceID` is really breed code linkage
- `ColorID` is really old color code linkage
- `HairID` is really old hair code linkage
- `CurrentOwnerId` is not the current `users.id`; it is a legacy owner code in the `PrevDog::legacyOwner()` path

## Highest-value model/table mappings

### `PrevDog` -> `DogsDB`

File: `app\Models\PrevDog.php`

Primary operational identity split:

- physical PK: `id`
- business identity used across legacy relations: `SagirID`

Important columns and meaning:

- `id`: surrogate PK for the row itself
- `DataID`: original legacy data identifier
- `SagirID`: the real dog identity used across pedigree, shows, and many legacy joins
- `Heb_Name`, `Eng_Name`: display names
- `RaceID`: breed code, joins to `BreedsDB.BreedCode`
- `ColorID`: color code, joins to `ColorsDB.OldCode`
- `HairID`: hair code, joins to `HairsDB.OldCode`
- `CurrentOwnerId`: old-style current owner link, joins to `users.owner_code`
- `FatherSAGIR`, `MotherSAGIR`: self joins to `DogsDB.SagirID`
- `BeitGidulID`: breeding-house code, joins to `breedinghouses.GidulCode`
- `GenderID`, `SizeID`, `Status`: normalized via custom casts / enums
- `pedigree_color`, `sagir_prefix`: enum-backed presentation logic
- `DnaID`, `Chip`, `Chip_2`: breeding and identity verification signals
- `red_pedigree`: important breeding/pedigree restriction flag
- `Breeding_ManagerID`: direct link to `PrevUser.id`

Important app-level relations:

- `breed()` uses `RaceID -> BreedCode`
- `color()` uses `ColorID -> OldCode`
- `hair()` uses `HairID -> OldCode`
- `father()` / `mother()` use `FatherSAGIR` / `MotherSAGIR -> SagirID`
- `owners()` uses pivot `dogs2users` with `SagirID` on the dog side and `id` on the user side
- `legacyOwner()` uses `CurrentOwnerId -> owner_code`

Critical special case:

- The app supports both the old direct owner relationship (`CurrentOwnerId -> owner_code`) and the newer pivot-based
  owner
  relationship (`dogs2users`). Any rewrite must preserve both semantics or explicitly define a cutoff rule.

### `PrevUser` -> `users`

File: `app\Models\PrevUser.php`

Important identity fields:

- `id`: row PK and most pivot-table linkage target
- `owner_code`: legacy owner business key used by `PrevDog::legacyOwner()`
- `record_source`: helps differentiate legacy record types

Important columns / semantics from code:

- `owner_code`: legacy owner linkage key
- `info_id`, `sagir_owner_id`, `order_id`, `new_org_data_id`: historical ownership / migration metadata
- `club_id`, `member_status`, `breed_id`, `beit_gidul_id`: denormalized current relations / labels in some screens
- phone fields are normalized through `normalisedPhone()` and `normaliseMsisdn()`

Important relations:

- `dogs()` uses `dogs2users.user_id -> users.id` and `dogs2users.sagir_id -> DogsDB.SagirID`
- `history_dogs()` uses `DogsDB.CurrentOwnerId -> users.owner_code`
- `clubs()` uses `club2user`
- `user()` links legacy `PrevUser.id` to modern `User.prev_user_id`

Critical special case:

- `PrevUser.id` and `PrevUser.owner_code` are both live identifiers with different meanings. Confusing them will produce
  wrong owners.

### `PrevBreed` -> `BreedsDB`

File: `app\Models\PrevBreed.php`

Important columns:

- `id`: row PK
- `DataID`: original record id
- `BreedCode`: business key used by dogs and show-breed rows
- `BreedName`, `BreedNameEN`: business labels
- `GroupID`, `FCICODE`: classification
- `UserManagerID`, `ClubManagerID`: legacy manager references

Critical special case:

- `BreedCode` is the join target for dogs and show-breed rows, not `id`.

### `PrevColor` -> `ColorsDB`

File: `app\Models\PrevColor.php`

Important columns:

- `id`
- `OldCode`: business key used by dogs via `ColorID`
- `ColorNameHE`, `ColorNameEN`
- `Remark`

### `PrevHair` -> `HairsDB`

File: `app\Models\PrevHair.php`

Important columns:

- `id`
- `OldCode`: business key used by dogs via `HairID`
- `HairNameHE`, `HairNameEN`
- `Remark`

### `PrevClub` -> `clubs`

File: `app\Models\PrevClub.php`

Important columns:

- `id`
- `ClubCode`: business code
- `Name`, `EngName`
- `RegistrationPrice`, `GeneralReviewFee`, `DogReviewFee`, `Breed_NonReg_Price`, `PerDog_NonReg_Price`, `TestPrice`
- `ManagerID`: likely legacy manager code linkage

Important app behavior:

- club membership and breeder discounts are resolved in services, not only in raw model reads
- fee columns are interpreted in `PrevClubMembershipResolverService`

### `PrevBreedingHouse` -> `breedinghouses`

File: `app\Models\PrevBreedingHouse.php`

Important columns:

- `id`
- `GidulCode`: business code used by dogs through `BeitGidulID`
- `HebName`, `EngName`
- `MegadelCode`: legacy owner/manager code
- status and recommendation flags

Critical special case:

- Dogs relate to breeding houses through `BeitGidulID -> GidulCode`, not `id`.

## Breeding-related legacy tables

### `PrevBreeding` -> `breedings`

Key columns:

- `id`
- `SagirId`: female dog business id (`DogsDB.SagirID`)
- `MaleSagirId`: male dog business id (`DogsDB.SagirID`)
- validation flags such as `BreedMismatch`, `Male_DNA`, `Female_DNA`, `Male_Breeding_Not_Approved`,
  `Female_Breeding_Not_Approved`
- litter counts and birthing data
- payment fields
- `breeding_house_id`
- `Breeding_ManagerID`

Critical special case:

- This table mixes validation outcome flags, litter result counts, and payment metadata in one record.

### `PrevBreedingRelatedDog` -> `breeding_related_dog`

Key columns:

- `id`
- `sagir_id`
- `mother_sagir_id`
- `breeding_id`
- `color`, `hair`, gender / approval metadata
- attached image / document paths

## Show-related legacy tables

### `PrevShow` -> `ShowsDB`

File: `app\Models\PrevShow.php`

Key columns:

- `id`
- `TitleName`, `LongDesc`, `ShortDesc`
- `StartDate`, `EndDate`, `EndRegistrationDate`
- `ShowType`, `ShowStatus`
- `ClubID`
- price columns `Dog2Price1..Dog2Price10`, `ShowPrice`, `CouplesPrice`, `BGidulPrice`, `ZezaimPrice`, `YoungPrice`,
  `MoreDogsPrice*`, `TicketCost`, `PeototCost`
- `start_from_index`, `location`, `Check_all_members`

Critical special case:

- `PrevShowResource` directly queries `shows_results` through `DB::connection('mysql_prev')` to implement table filters.

### `PrevShowArena` -> `Shows_Structure`

Key columns typically represent arena / structure rows for a show and are joined from show/breed/result records.

### `PrevShowBreed` -> `Shows_Breeds`

Key columns:

- `ShowID`
- `ArenaID`
- `RaceID`
- `JudgeID`
- `OrderID`

Critical special case:

- `RaceID` maps to `BreedsDB.BreedCode`, not `BreedsDB.id`.

### `PrevShowClass` -> `Shows_Classes`

Class rows for each show / breed / arena flow.

### `PrevShowDog` -> `Shows_Dogs_DB`

Dog participation records. These are not the same as `DogsDB` rows; they are show-entry rows tied back to dog identity.

### `PrevShowRegistration` -> `shows_registration`

Registration rows per show entry.

### `PrevShowPayment` -> `shows_payments_info`

Payment rows associated with registrations / shows.

### `PrevShowResult` -> `shows_results`

Critical special case already documented in project research:

- Arena linkage uses `MainArenaID`, not `ArenaID`.

## Ownership / pivot / bridge tables

### `PrevUserDog` -> `dogs2users`

Important columns:

- `user_id`: points to `PrevUser.id`
- `sagir_id`: points to `PrevDog.SagirID`
- `status`: used to distinguish current vs old ownership
- `is_current_owner`, `Show_In_Pedigree`

### `PrevClubUser` -> `club2user`

Important columns:

- `user_id`: `PrevUser.id`
- `club_id`: `PrevClub.id`
- `expire_date`, `type`, `status`, `payment_status`, `forbidden`

Important app rule:

- Membership validity in services is not just existence; it also depends on expiry, soft deletion, and `payment_status`.

### `PrevBreedingHouseUser` -> `breedhouses2users`

Bridge between legacy users and breeding houses.

### `PrevBreedClub` -> `breed_club`

Bridge between breeds and clubs.

### `PrevBreedUser` -> `user2breeds`

Bridge between users and breeds.

## Used `mysql_prev` tables inventory

This is the direct used-table set confirmed from `app\Models\Prev*.php` plus direct `mysql_prev` query hotspots.

| Model / usage                      | Table                  | Notes                                    |
|------------------------------------|------------------------|------------------------------------------|
| `PrevBreed`                        | `BreedsDB`             | Dogs and show-breed rows use `BreedCode` |
| `PrevBreedClub`                    | `breed_club`           | Breed-club bridge                        |
| `PrevBreedUser`                    | `user2breeds`          | User-breed bridge                        |
| `PrevBreeding`                     | `breedings`            | Female/male breeding by `SagirID`        |
| `PrevBreedingHouse`                | `breedinghouses`       | Dogs use `BeitGidulID -> GidulCode`      |
| `PrevBreedingHouseUser`            | `breedhouses2users`    | Bridge                                   |
| `PrevBreedingRelatedDog`           | `breeding_related_dog` | Litter / related pup rows                |
| `PrevClub`                         | `clubs`                | Membership, fees, discounts              |
| `PrevClubManager`                  | `user_club_manager`    | Club manager bridge                      |
| `PrevClubUser`                     | `club2user`            | Membership pivot                         |
| `PrevColor`                        | `ColorsDB`             | Dogs join via `OldCode`                  |
| `PrevDog`                          | `DogsDB`               | Core dog table                           |
| `PrevDogDocument`                  | `dogs_documents`       | DNA / tests / maag docs                  |
| `PrevDogImport`                    | `DogsInfo`             | Imported dog requests                    |
| `PrevDogTitle`                     | `Dogs_ScoresDB`        | Dog awards / scores                      |
| `PrevHair`                         | `HairsDB`              | Dogs join via `OldCode`                  |
| `PrevHealth`                       | `health`               | Health records                           |
| `PrevJudge`                        | `JudgesDB`             | Judges                                   |
| `PrevOwnerFile`                    | `owner_files`          | Legacy owner files                       |
| `PrevPayment`                      | `payment`              | Legacy payments                          |
| `PrevPrice`                        | `pricing`              | Legacy pricing                           |
| `PrevShow`                         | `ShowsDB`              | Shows                                    |
| `PrevShowArena`                    | `Shows_Structure`      | Arenas / structure                       |
| `PrevShowBreed`                    | `Shows_Breeds`         | Breed assignment by show arena           |
| `PrevShowClass`                    | `Shows_Classes`        | Show classes                             |
| `PrevShowDog`                      | `Shows_Dogs_DB`        | Show-entry dogs                          |
| `PrevShowPayment`                  | `shows_payments_info`  | Show payments                            |
| `PrevShowRegistration`             | `shows_registration`   | Registrations                            |
| `PrevShowResult`                   | `shows_results`        | Results; special `MainArenaID` semantics |
| `PrevSkill`                        | `skills`               | Skills master                            |
| `PrevSkillUser`                    | `users_skills`         | User-skill bridge                        |
| `PrevTitle`                        | `dogs_titles_db`       | Titles master                            |
| `PrevUser`                         | `users`                | Legacy people / owners / members         |
| `PrevUserActivity`                 | `UserActivities`       | Activity log                             |
| `PrevUserDog`                      | `dogs2users`           | Owner pivot                              |
| `PrevUserRequest`                  | `public_registration`  | Public user requests                     |
| `PrevUserTask`                     | `users_tasks`          | Task rows                                |
| `PrevVetAuth`                      | `agra_cities`          | Vet auth / city rows                     |
| direct query in `PrevShowResource` | `shows_results`        | Used for `has_results` filter            |

## Full-column sources to consult alongside this guide

Because the legacy surface is large, use these together with this guide:

- `docs\Prev-All-Tables-Usage.md`
- `docs\Prev-All-Tables-W2-Usage.md`
- `docs\legacy-data-research\overview.csv`
- Boost `database-schema` on `mysql_prev` for filtered tables

## Known data-quality / schema-quality issues already compensated for in code

1. FK-like values stored as decimals
2. business owner keys mixed with PK keys
3. duplicate representations of current ownership
4. multilingual and partially denormalized labels
5. legacy/new timestamps coexisting on the same row (`CreationDateTime` vs `created_at`)
6. inconsistent boolean storage (`decimal`, `tinyint`, `varchar`)
7. many tables without formal foreign keys despite application-level relationships
