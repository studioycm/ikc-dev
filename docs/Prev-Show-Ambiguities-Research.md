# Previous Show Ambiguities — Deep Column Research and Relationship Validation

Context: Legacy data in MySQL (connection: mysql_prev, DB: rbzmwnvqjq). This research extends the prior report with
deeper statistics for the specified columns, confirms the Shows_Dogs_DB.BreedID ambiguity, and provides a CSV with
summarized stats.

- Read‑only analysis; no data mutations.
- Windows: W1 = rows where COALESCE(created_at, CreationDateTime) < 2022‑03‑01 (or CreationDateTime where created_at is
  absent), W2 = >= 2022‑03‑01.
- Sources profiled: ShowsDB, Shows_Structure, Shows_Breeds, Shows_Classes, Shows_Dogs_DB, shows_results,
  shows_registration, shows_payments_info, JudgesDB, show_winners. Cross‑checks also include DogsDB and BreedsDB.

---

## 1) Ambiguity Confirmation: Shows_Dogs_DB.BreedID

Questions:

- Does Shows_Dogs_DB.BreedID represent a BreedCode (BreedsDB.BreedCode) or a link to an Arena Breed row (
  Shows_Breeds.DataID)?

Findings (aggregates on mysql_prev):

- Overall: total ShowDogs = 66,895; BreedID empty = 2.
    - BreedID = BreedsDB.BreedCode: 66,893 (≈ 100%).
    - BreedID = Shows_Breeds.DataID: 29,763 (44.5%).
- Per windows:
    - W1 (< 2022‑03‑01): total 43,072; empty 2; matches BreedCode 43,070; matches Shows_Breeds.DataID 19,151; both match
      19,151; match Shows_Breeds.DataID within the same ShowID: 203.
    - W2 (>= 2022‑03‑01): total 23,823; empty 0; matches BreedCode 23,823; matches Shows_Breeds.DataID 10,612; both
      match 10,612; match within same ShowID: 0.

Conclusion:

- BreedID on Shows_Dogs_DB overwhelmingly represents a BreedCode. The overlap with Shows_Breeds.DataID is incidental and
  disappears per‑show post‑2022. Keep PrevShowDog::breed as BreedID → BreedsDB.BreedCode.
- If a per‑show Arena Breed link is needed, prefer a dedicated column (ShowBreedID) for the relation to
  Shows_Breeds.DataID. Today, ShowBreedID is mostly empty historically (W1=all empty; W2 sparsely used), so using
  BreedID for a hasMany from PrevShowBreed is unreliable for modern data.

Related validations:

- DogsDB.RaceID → BreedsDB.BreedCode matches for 259,196/259,230 (≈ 99.99%).
- Shows_Breeds.RaceID → BreedsDB.BreedCode matches for 100% in both windows.
- shows_results.BreedID → BreedsDB.BreedCode: W1 8,282/8,307; W2 21,561/21,561.

---

## 2) Highlights by Table (Selected Columns)

Numbers below are per window. Empty = NULL or ''. Distinct counts exclude empty.

### ShowsDB

- W1 (total 343): DataID empty 14 (95.92% non‑empty), CreationDateTime empty 14; TitleName empty 0 (avg len 24.83),
  LongDesc empty 319; MaxRegisters empty 319; ShowType empty 19; ClubID empty 262; ShowStatus empty 33; FreeTextDesc
  empty 343; created_at empty 329; deleted_at non‑null 3; start_from_index empty 0; location empty 342;
  Check_all_members empty 343.
- W2 (total 559): DataID empty 559; CreationDateTime empty 559; TitleName empty 0 (avg len 36.87); LongDesc empty 0 (avg
  len 135.67); MaxRegisters empty 0; ShowType empty 0; ClubID empty 0; ShowStatus empty 0; FreeTextDesc empty 559;
  created_at empty 0; deleted_at non‑null 32; start_from_index empty 97; location empty 38; Check_all_members empty 527.

Interesting: Post‑2022 the legacy DataID/CreationDateTime are unused; core fields are consistently populated;
FreeTextDesc is unused.

### Shows_Structure

- W1 (total 1,479): DataID empty 31; CreationDateTime empty 31; ShowID empty 0; GroupName empty 2; GroupParentID empty
  755; ClassID empty 1,479; ArenaType empty 1,436; created_at empty 0; deleted_at non‑null 19; JudgeID empty 1,478 (
  match Judges 1); arena_date empty 1,479; OrderTime empty 1,479.
- W2 (total 1,207): DataID empty 1,207; CreationDateTime empty 1,207; ShowID empty 0; GroupName empty 0; GroupParentID
  empty 1,207; ClassID empty 1,207; ArenaType empty 0; created_at empty 0; deleted_at non‑null 184; JudgeID empty 907 (
  matches Judges 299); arena_date empty 1,164; OrderTime empty 1,124.

Interesting: ArenaType is fully populated since 2022; JudgeID remains sparse; arena_date introduced but mostly empty.

### Shows_Breeds

- W1 (total 7,482): DataID empty 0; RaceID empty 0 (distinct 212; 100% match to BreedCode); ArenaID/ShowID empty 0;
  JudgeID empty 697.
- W2 (total 14,107): DataID empty 0; RaceID empty 0 (distinct 253; 100% match to BreedCode); ArenaID/ShowID empty 0;
  JudgeID empty 9,826.

Interesting: Judge assignment largely missing post‑2022; RaceID mapping is solid.

### Shows_Classes

- W1 (total 137,306): minimal empties on ClassName/GenderID; Status empty 42; ShowArenaID empty 8; many class flags
  empty (historical defaults). BreedID always set, distinct 219.
- W2 (total 303,775): core fields fully populated; class flags default to empty; ShowMainArenaID unused post‑2022.

### Shows_Dogs_DB

- W1 (total 43,072): BreedID empty 2 (distinct 204); SagirID distinct 12,337; OwnerID empty 43,072; MainArenaID empty
  12; ArenaID empty 7; ShowBreedID empty 43,072; new_show_registration_id empty 43,060; OwnerMobile empty 27,139 (avg
  len 10.20); OwnerEmail empty 23,866 (avg 19.65); present empty 43,070.
- W2 (total 23,823): BreedID empty 0 (distinct 184); OwnerID empty 23,823; MainArenaID empty 23,823; ArenaID empty
  1,130; ShowBreedID empty 23,226; new_show_registration_id empty 3; OwnerMobile empty 4 (avg len 8.99); OwnerEmail
  empty 106 (avg 20.50); present empty 23,500.

Interesting: OwnerID/BeitGidulName are effectively unused here; contact info completeness greatly improved post‑2022;
ShowBreedID started to appear but remains sparse.

### shows_results

- W1 (total 8,307): RegDogID empty 0 (but join to shows_registration.id returned 0 matches — legacy mismatch); SagirID
  match to DogsDB 165; ClassID/ShowID/MainArenaID match 100%; SubArenaID empty for all; BreedID empty 25 (match 8,282).
- W2 (total 21,561): All FK fields present; Dogs match 21,560; Class/Show/MainArena all match 100%; SubArenaID empty for
  all; BreedID match 21,561.

### shows_registration

- W1 (total 460): ClassID empty for all; registered_by empty for all; many owner/address fields empty by design; Dogs
  match 445/460.
- W2 (total 27,849): SagirID empty 119; ClassID empty 2,159; registered_by empty 6,534; Dogs match 27,744; ClassID
  matches 25,690.

### shows_payments_info

- W1 (total 377): SagirID empty 1; RegistrationID matches 323/377; PaymentAmount min 115, max 1,050, avg ~256.72.
- W2 (total 20,634): SagirID empty 2; RegistrationID matches 20,632/20,634; PaymentAmount min 5, max 3,300, avg ~262.49.

### JudgesDB

- W1 (total 284): CreationDateTime present; BreedID empty 277 (rare usage); Country always present (56 distinct); names
  present in HE/EN.
- W2 (total 93): BreedID empty 93; Country present (38 distinct); names present.

### show_winners

- W1: none.
- W2 (total 28): Optional blocks (couples, breeding_houses, puppies) sparsely populated; created_at present.

---

## 3) Relationship Notes and Recommendations

- Authoritative mappings:
    - ShowDog.BreedID → BreedsDB.BreedCode (keep PrevShowDog::breed as is).
    - ShowBreed.RaceID → BreedsDB.BreedCode (PrevShowBreed::breed is correct).
    - Result.BreedID → BreedsDB.BreedCode (good post‑2022).
- For linking ShowBreed to ShowDog, prefer ShowDog.ShowBreedID → Shows_Breeds.DataID. The current hasMany on
  PrevShowBreed using ShowDog.BreedID is unreliable and should be considered legacy/backward‑compat only.
- SubArenaID in results is unused; consumers should not rely on it.
- ShowsDB legacy DataID/CreationDateTime are obsolete post‑2022.

Open items for confirm:

- Whether to refactor PrevShowBreed::showDogs to map ShowBreedID → DataID when ShowBreedID is present, and fall back to
  BreedID → BreedCode only for very old rows.
- Whether RegDogID in shows_results should be joined to a different registration key historically.

---

## 4) CSV Stats

A machine‑readable summary was generated at:

- docs\\prev_show_stats.csv

Columns: table,column,window,total,empty_count,non_empty_count,non_empty_pct,distinct_non_empty,notes

---

## 5) Reproducibility Queries (snippets)

- BreedID ambiguity (per windows):

```sql
SELECT 'W1' AS win,
  COUNT(*) AS total,
  SUM(d.BreedID IS NULL OR d.BreedID='') AS breedid_empty,
  SUM(EXISTS (SELECT 1 FROM BreedsDB b WHERE b.BreedCode = d.BreedID)) AS match_breedcode,
  SUM(EXISTS (SELECT 1 FROM Shows_Breeds sb WHERE sb.DataID = d.BreedID)) AS match_showbreed,
  SUM(EXISTS (SELECT 1 FROM Shows_Breeds sb WHERE sb.DataID = d.BreedID AND sb.ShowID = d.ShowID)) AS match_showbreed_same_show
FROM Shows_Dogs_DB d
WHERE COALESCE(d.created_at,d.CreationDateTime) < '2022-03-01'
UNION ALL
SELECT 'W2' AS win,
  COUNT(*) AS total,
  SUM(d.BreedID IS NULL OR d.BreedID='') AS breedid_empty,
  SUM(EXISTS (SELECT 1 FROM BreedsDB b WHERE b.BreedCode = d.BreedID)) AS match_breedcode,
  SUM(EXISTS (SELECT 1 FROM Shows_Breeds sb WHERE sb.DataID = d.BreedID)) AS match_showbreed,
  SUM(EXISTS (SELECT 1 FROM Shows_Breeds sb WHERE sb.DataID = d.BreedID AND sb.ShowID = d.ShowID)) AS match_showbreed_same_show
FROM Shows_Dogs_DB d
WHERE COALESCE(d.created_at,d.CreationDateTime) >= '2022-03-01';
```

- Integrity checks (examples) for results/registrations/payments are available in the repository history and can be
  rerun via TinkerWell.

---

Prepared by: IKC Dev — Legacy Data Profiling
Generated on: {{ date('Y-m-d') }}
