# Samples (Show and Arena)

Generated at: 2025-08-23 20:41:49

Files:

- sample_top_shows_by_dogs.csv
- sample_top_shows_by_payments.csv
- sample_shows_intersection.csv (or fallback from top dogs if intersection is empty)
- sample_arenas_for_show_1124.csv (if a show was auto-selected)
- samples.csv (auto-picked show/arena with notes)

Notes:

- Breeds per arena are constrained to those with at least one show-dog in that arena (EXISTS filter).
- Results are linked by shows_results.MainArenaID -> Shows_Structure.id.
- This command is read-only and does not mutate any data.
