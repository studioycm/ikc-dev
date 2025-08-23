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
