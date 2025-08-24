# Findings Synthesis

A running summary of insights, anomalies, and recommended retrieval patterns discovered during legacy data research.

- Judges are linked per arena via Shows_Breeds.JudgeID (deprecated Shows_Structure.JudgeID ignored).
- Dogs link to Breeds via Shows_Dogs_DB.BreedID -> BreedsDB.BreedCode.
- Payments aggregate by RegistrationID; to scope to a Show use Registrations (shows_registration.ShowID).
- Prefer eager loading of arena, breed, judge to avoid N+1.

See individual reports for details.

- Judges per arena (update): breeds_count includes only breeds that had at least one show dog in that same arena of that
  show (Shows_Breeds filtered via EXISTS on Shows_Dogs_DB by ShowID, ArenaID, BreedID; excluding soft-deleted dogs).
- Results linkage (clarified): shows_results.MainArenaID maps to Shows_Structure.id. Dog→Result matching should use
  ShowID + ArenaID (via MainArenaID) + SagirID, and often ClassID when present. Note: ShowOrderID is not used since W2 (
  March 2022).
- Legacy mapping: shows_results.RegDogID and shows_registrations.DogID refer to the dog record in DogsDB (legacy
  naming). Modern joins typically use SagirID.

## Temporal schedule sanity (what it means and how we’ll measure it)

Goal: validate that the planned running order (OrderID and/or OrderTime) per arena/class aligns with how results were
actually recorded, and flag anomalies.

Data points:

- From Shows_Dogs_DB: ShowID, ArenaID, ClassID, SagirID, OrderID, (optional) OrderTime, created_at/CreationDateTime.
- From shows_results: ShowID, MainArenaID (arena reference), ClassID, SagirID, CreationDateTime.

Core checks per show and per arena:

- Order coverage: percentage of dogs with non-null OrderID; list classes with low coverage.
- Linkage monotonicity: correlation between OrderID and the presence of a result for that dog (per arena/class). We’ll
  compute a rank correlation (Spearman-like) to see if higher OrderID dogs tend to be completed later; strong negative
  or near-zero may indicate out-of-order recording.
- Bucketed flow: results_per_order_bucket (e.g., 1–10, 11–20, …) versus dog counts; large gaps suggest delays or missing
  results.
- Outliers and contradictions:
  - Arenas/classes with many dogs but zero results.
  - Results for SagirIDs with null/absent OrderID.
  - Results in MainArenaID that have no corresponding dogs in the same arena.
  - Duplicate results for the same (ShowID, MainArenaID, ClassID, SagirID).

Interpretation guide:

- Minor deviations are expected (late edits). Systemic gaps (entire class missing results) should be called out.
- If OrderTime exists, we’ll compare chronological ordering (by timestamps) with numeric OrderID to surface scheduling
  anomalies.

Usage:

- Surface problematic arenas/classes before migration or complex UI builds.
- Inform Filament table default sorts (prefer OrderID when monotonicity holds; otherwise use Result timestamp).
