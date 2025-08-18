# Previous Show Management — Data Profiling, Relationship Mapping, and Model Conventions

Context: Legacy data lives in MySQL (connection: mysql_prev, DB: rbzmwnvqjq). This report provides:

- Column usage stats (non-empty vs empty/null) for specific tables/columns.
- Date-bounded stats for two windows: Until 2022-03-01 and From 2022-03-01 (inclusive).
- Relationship mapping across Show, Arena, Show Breed, Class, Judge, Show Registration, Show Dog, Show Result, Show
  Payment, Dog, Breed.
- Proposed Laravel 12 model conventions, namespaces, and casts based on the schema.

Notes:

- Read-only analysis; no data mutations.
- Date filter uses COALESCE(created_at, CreationDateTime) where available; otherwise a documented fallback.
- All queries executed against mysql_prev on rbzmwnvqjq.

---

## 1) Usage Stats per Table / Column (Two Windows)

Windows:

- W1: Until 2022-03-01 (exclusive)
- W2: From 2022-03-01 (inclusive)

Date filtering rule: COALESCE(created_at, CreationDateTime) except where created_at is absent, then CreationDateTime.

Below are key results. Percentages are relative to the row counts within each window.

### ShowsDB (shows)

Filter: COALESCE(created_at, CreationDateTime)

- W1 (total 343)
    - DataID non-empty: 329 (95.92%)
    - ClubID non-empty: 81 (23.62%)
    - ShowType non-empty: 324 (94.46%)
    - ShowStatus non-empty: 310 (90.38%)
    - ShowPrice non-empty: 90 (26.24%)
- W2 (total 559)
    - DataID non-empty: 0 (0.00%) [legacy DataID not used]
    - ClubID non-empty: 559 (100.00%)
    - ShowType non-empty: 559 (100.00%)
    - ShowStatus non-empty: 559 (100.00%)
    - ShowPrice non-empty: 0 (0.00%)

SQL used (example):

```sql
SELECT 'ShowsDB < 2022-03-01' AS window, COUNT(*) total,
  SUM(CASE WHEN `DataID` IS NOT NULL AND `DataID` <> '' THEN 1 ELSE 0 END) AS DataID_non_empty,
  ROUND(100*SUM(CASE WHEN `DataID` IS NOT NULL AND `DataID` <> '' THEN 1 ELSE 0 END)/NULLIF(COUNT(*),0),2) AS DataID_non_empty_pct
FROM `ShowsDB`
WHERE COALESCE(`created_at`,`CreationDateTime`) < '2022-03-01';
```

### Shows_Structure (arenas)

Filter: COALESCE(created_at, CreationDateTime)

- W1 (total 1,479)
    - GroupParentID empty: 755
    - ClassID empty: 1,479
    - ArenaType empty: 1,436
    - JudgeID empty: 1,478
    - arena_date empty: 1,479
- W2 (total 1,207)
    - GroupParentID empty: 1,207
    - ClassID empty: 1,207
    - ArenaType empty: 0
    - JudgeID empty: 907
    - arena_date empty: 1,164

### Shows_Breeds (arena breeds)

Filter: CreationDateTime (no created_at column)

- W1 (total 7,482)
    - MainArenaID empty: 14
    - JudgeID empty: 697
- W2 (total 14,107)
    - MainArenaID empty: 14,107
    - JudgeID empty: 9,826

### Shows_Classes (classes)

Filter: COALESCE(created_at, CreationDateTime)

- W1 (total 137,306)
    - ShowArenaID empty: 8
    - Status empty: 42
    - GenderID empty: 8
- W2 (total 303,775)
    - ShowArenaID empty: 0
    - Status empty: 0
    - GenderID empty: 0

### Shows_Dogs_DB (show dogs)

Filter: COALESCE(created_at, CreationDateTime)

- W1 (total 43,072)
    - new_show_registration_id empty: 43,060
    - MainArenaID empty: 12; ArenaID empty: 7
    - BreedID empty: 2
    - OwnerID empty: 43,072
- W2 (total 23,823)
    - new_show_registration_id empty: 3
    - ClassID empty: 3
    - MainArenaID empty: 23,823; ArenaID empty: 1,130
    - BreedID empty: 0
    - OwnerID empty: 23,823

### shows_results (results)

Filter: COALESCE(created_at, CreationDateTime)

- W1 (total 8,307)
    - ShowOrderID empty: 8,307
    - SubArenaID empty: 8,307
- W2 (total 21,561)
    - DataID/RegDogID/SagirID/ClassID/ShowID all non-empty: 100%

### shows_registration (registrations)

Filter: COALESCE(created_at, CreationDateTime)

- W1 (total 460)
    - ClassID empty: 460
    - registered_by empty: 460
- W2 (total 27,849)
    - SagirID empty: 119
    - ClassID empty: 2,159
    - registered_by empty: 6,534

### shows_payments_info (payments)

Filter: COALESCE(created_at, CreationDateTime)

- W1 (total 377)
    - SagirID empty: 1
- W2 (total 20,634)
    - SagirID empty: 2

Generic SQL template used for per-column usage:

```sql
SELECT 'TABLE window' AS window,
  COUNT(*) AS total,
  SUM(CASE WHEN `Col` IS NULL OR `Col` = '' THEN 1 ELSE 0 END) AS Col_empty,
  (COUNT(*) - SUM(CASE WHEN `Col` IS NULL OR `Col` = '' THEN 1 ELSE 0 END)) AS Col_non_empty,
  ROUND(100*(COUNT(*) - SUM(CASE WHEN `Col` IS NULL OR `Col` = '' THEN 1 ELSE 0 END))/NULLIF(COUNT(*),0),2) AS Col_non_empty_pct
FROM `Table`
WHERE COALESCE(`created_at`, `CreationDateTime`) < '2022-03-01';
```

---

## 2) Relationship Mapping

Entities and primary keys:

- Show = ShowsDB (pk: id)
- Arena = Shows_Structure (pk: id)
- Arena Breed = Shows_Breeds (pk: DataID)
- Judge = JudgesDB (pk: DataID)
- Show Class = Shows_Classes (pk: id)
- Show Dog = Shows_Dogs_DB (pk: id)
- Show Result = shows_results (pk: DataID)
- Show Registration = shows_registration (pk: id)
- Show Payment = shows_payments_info (pk: DataID)
- Dog = DogsDB (pk: SagirID)
- Breed = BreedsDB (pk: id; domain key: BreedCode)

Foreign keys and notes (non-standard FKs called out):

- Shows_Structure.ShowID → ShowsDB.id
- Shows_Structure.JudgeID → JudgesDB.DataID
- Shows_Breeds.ShowID → ShowsDB.id
- Shows_Breeds.ArenaID → Shows_Structure.id
- Shows_Breeds.RaceID → BreedsDB.BreedCode (non-standard FK by code)
- Shows_Breeds.JudgeID → JudgesDB.DataID
- Shows_Classes.ShowID → ShowsDB.id
- Shows_Classes.ShowArenaID → Shows_Structure.id
- Shows_Classes.BreedID → BreedsDB.BreedCode (non-standard FK by code; to confirm)
- Shows_Dogs_DB.ShowID → ShowsDB.id
- Shows_Dogs_DB.ArenaID → Shows_Structure.id
- Shows_Dogs_DB.ClassID → Shows_Classes.id
- Shows_Dogs_DB.ShowRegistrationID → shows_registration.id
- Shows_Dogs_DB.new_show_registration_id → shows_registration.id (introduced later; sparse pre-2022)
- Shows_Dogs_DB.SagirID → DogsDB.SagirID (non-standard pk)
- Shows_Dogs_DB.BreedID → BreedsDB.BreedCode (non-standard FK by
  code) [Ambiguity: also used as FK to Shows_Breeds.DataID in some legacy logic]
- shows_results.ShowID → ShowsDB.id
- shows_results.MainArenaID/SubArenaID → Shows_Structure.id
- shows_results.ClassID → Shows_Classes.id
- shows_results.RegDogID → shows_registration.id
- shows_results.SagirID → DogsDB.SagirID (non-standard pk)
- shows_results.BreedID → BreedsDB.BreedCode (non-standard FK by code)
- shows_registration.ShowID → ShowsDB.id
- shows_registration.SagirID → DogsDB.SagirID
- shows_registration.ClassID → Shows_Classes.id
- shows_registration.registered_by → users.id (PrevUser)
- shows_payments_info.RegistrationID → shows_registration.id
- shows_payments_info.SagirID → DogsDB.SagirID

Ambiguities needing confirmation:

- Shows_Dogs_DB.BreedID:
    - PrevShowDog::breed maps BreedID → BreedsDB.BreedCode.
    - PrevShowBreed::showDogs maps ShowDog.BreedID → Shows_Breeds.DataID.
    - Action: decide authoritative meaning and consider adding separate columns or a view to disambiguate.
- Legacy DataID columns in ShowsDB appear unused post-2022 (0% non-empty in W2).

---

## 3) Models, Namespaces, and Casts Proposal

Namespace convention: Prefer App\Models\Prev\Xxx for legacy models; current project uses flat App\Models\PrevXxx. Either
keep current for stability or introduce namespaced classes with class aliases. Recommendation: keep current now;
transition to App\Models\Prev\ later with careful refactors.

Casts (focus on decimal/double/float and id-like decimals):

- PrevShow (ShowsDB)
    - Monetary: ShowPrice, Dog2Price1..10, CouplesPrice, BGidulPrice, ZezaimPrice, YoungPrice, MoreDogsPrice,
      MoreDogsPrice2, TicketCost, PeototCost → float
    - Integers: MaxRegisters → int; ShowType, ClubID, ShowStatus, Check_all_members, start_from_index → int
    - Datetimes: StartDate, EndDate, EndRegistrationDate, created_at, updated_at, deleted_at, CreationDateTime,
      ModificationDateTime → datetime

- PrevShowArena (Shows_Structure)
    - Integers: ShowID, GroupParentID, ClassID, OrderID, ArenaType, JudgeID → int
    - Datetimes: arena_date, OrderTime, created_at, updated_at, deleted_at, CreationDateTime, ModificationDateTime →
      datetime

- PrevShowBreed (Shows_Breeds)
    - Integers: ShowID, ArenaID, MainArenaID, RaceID (domain code), JudgeID, OrderID → int
    - Note: RaceID maps to BreedsDB.BreedCode.

- PrevShowClass (Shows_Classes)
    - Integers: Age_FromMonths, Age_TillMonths, ShowID, ShowArenaID, SpecialClassID, OrderID, GenderID, BreedID,
      ShowMainArenaID, AwardIDClass, Status and class flags → int
    - Datetimes: created_at, updated_at, deleted_at, CreationDateTime, ModificationDateTime → datetime

- PrevShowDog (Shows_Dogs_DB)
    - Integers: ShowID, SagirID, OrderID, OwnerID, BreedID, ClassID, ShowRegistrationID, new_show_registration_id,
      MainArenaID, ArenaID, ShowBreedID, SizeID, GenderID → int
    - Datetimes: BirthDate, present, created_at, updated_at, deleted_at, CreationDateTime, ModificationDateTime →
      datetime
    - Strings: OwnerEmail, OwnerMobile, DogName, GlobalSagirID, BeitGidulName, HairID, ColorID → string

- PrevShowResult (shows_results)
    - Integers: DataID, RegDogID, SagirID, ShowOrderID, MainArenaID, SubArenaID, ClassID, ShowID → int
    - Award flags (JCAC, GCAC, REJCAC, REGCAC, CW, BJ, etc.) → int
    - Datetimes: created_at, updated_at, deleted_at, CreationDateTime, ModificationDateTime → datetime

- PrevShowRegistration (shows_registration)
    - Integers: id, ShowID, SagirID, ClassID, registered_by, number_in_ring, status → int (registered_by may be double
      in DB; cast to int for app usage)
    - Datetimes: created_at, updated_at, deleted_at, CreationDateTime, ModificationDateTime → datetime

- PrevShowPayment (shows_payments_info)
    - Integers: DataID, SagirID, RegistrationID, DogID → int
    - Monetary: PaymentAmount → float
    - Datetimes: created_at, updated_at, deleted_at, CreationDateTime, ModificationDateTime → datetime

Relationship methods (explicit keys and returns), example signatures:

```php
public function registrations(): \Illuminate\Database\Eloquent\Relations\HasMany { /* ShowID → id */ }
public function arena(): \Illuminate\Database\Eloquent\Relations\BelongsTo { /* ArenaID → id */ }
public function breed(): \Illuminate\Database\Eloquent\Relations\BelongsTo { /* BreedID/RaceID → BreedCode */ }
public function dog(): \Illuminate\Database\Eloquent\Relations\BelongsTo { /* SagirID → SagirID */ }
public function registration(): \Illuminate\Database\Eloquent\Relations\BelongsTo { /* RegDogID/RegistrationID → id */ }
```

Areas needing confirmation:

- Shows_Dogs_DB.BreedID meaning (BreedCode vs Shows_Breeds.DataID) — see ambiguity above.
- Any places where CreationDateTime is sparsely populated; if empty, fall back to ShowsDB.StartDate for windowing.

---

## 4) Validation Plan

Eloquent (read-only) spot checks per relationship.

Examples:

- Show → Registrations / Dogs / Results / Payments

```php
// total registrations per show
PrevShow::on('mysql_prev')->withCount('registrations')->orderByDesc('registrations_count')->limit(5)->get();

// dogs joined via ShowID
PrevShow::on('mysql_prev')->withCount('showDogs')->orderByDesc('show_dogs_count')->limit(5)->get();

// results count sample for a given show id
PrevShow::on('mysql_prev')->find($id)->results()->count();
```

- Arena → Classes / Arena Breeds

```php
PrevShowArena::on('mysql_prev')->withCount(['classes','showBreeds'])->orderByDesc('classes_count')->limit(5)->get();
```

- Arena Breed → Show / Arena / Breed / Judge

```php
PrevShowBreed::on('mysql_prev')->with(['show','arena','breed','judge'])->limit(10)->get();
```

- Class → Show / Arena / Dogs

```php
PrevShowClass::on('mysql_prev')->with(['show','arena'])->withCount('showDogs')->limit(10)->get();
```

- Show Dog → Show / Arena / Class / Registration / Dog / Breed

```php
PrevShowDog::on('mysql_prev')->with(['show','arena','showClass','registration','dog','breed'])->limit(10)->get();
```

- Result → Show / Arenas / Class / Registration / Dog / Breed

```php
PrevShowResult::on('mysql_prev')->with(['show','mainArena','subArena','class','registration','dog','breed'])->limit(10)->get();
```

- Registration → Show / Dog / Class / Owner / Payments

```php
PrevShowRegistration::on('mysql_prev')->with(['show','dog','class','owner','payments'])->limit(10)->get();
```

- Payment → Registration / Dog

```php
PrevShowPayment::on('mysql_prev')->with(['registration','dog'])->limit(10)->get();
```

Lightweight Pest/Livewire tests (outline):

- Authenticate and visit Filament resources for Shows, Arenas, Classes, Dogs; assert pages render and tables list
  records without N+1 using with(['...']).
- Example (Pest):

```php
it('lists previous shows without N+1', function () {
    $this->actingAs(User::factory()->create());
    livewire(\App\Filament\Resources\PrevShowResource\Pages\ListPrevShows::class)
        ->assertStatus(200);
});
```

- Add dataset-driven assertions that counts from Eloquent match raw SQL counts for selected shows.

---

## 5) Reproducibility: SQL & Tinker Snippets

- SQL templates included above.
- Tinker snippets (read-only):

```php
// Top shows by registrations since 2022-03-01
PrevShow::on('mysql_prev')
  ->whereDate(DB::raw('COALESCE(created_at, CreationDateTime)'),'>=','2022-03-01')
  ->withCount('registrations')
  ->orderByDesc('registrations_count')
  ->limit(10)
  ->get(['id','TitleName','StartDate']);

// Validate breed mapping via BreedCode
PrevShowResult::on('mysql_prev')->whereNotNull('BreedID')->with('breed')->limit(10)->get(['BreedID']);

// Check ambiguous ShowDog->breed mapping
PrevShowDog::on('mysql_prev')->whereNotNull('BreedID')->with('breed')->limit(10)->get(['BreedID']);
```

---

### Appendix A: Date Filters per Table

- ShowsDB: COALESCE(created_at, CreationDateTime)
- Shows_Structure: COALESCE(created_at, CreationDateTime)
- Shows_Breeds: CreationDateTime
- Shows_Classes: COALESCE(created_at, CreationDateTime)
- Shows_Dogs_DB: COALESCE(created_at, CreationDateTime)
- shows_results: COALESCE(created_at, CreationDateTime)
- shows_registration: COALESCE(created_at, CreationDateTime)
- shows_payments_info: COALESCE(created_at, CreationDateTime)

---

Prepared by: IKC Dev — Legacy Data Profiling
Generated on: {{ date('Y-m-d') }}
