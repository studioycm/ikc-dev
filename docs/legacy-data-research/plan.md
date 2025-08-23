# IKC Legacy Show Data — Research Plan

This plan tracks the research tasks for analyzing legacy show data on connection `mysql_prev` without mutating any data.
Output files will be generated in this folder via dedicated Artisan commands.

## Scope & Constraints

- Do NOT change PHP app logic related to business features or database schema.
- Only: run read-only queries, add tests, create Artisan research scripts, produce Markdown/CSV.
- Stack: Laravel 12.21+, Filament v3, PHP 8.4, MySQL.
- Legacy connection: `mysql_prev`; New app data: `mysql`.
- Prefer Eloquent/Query Builder; raw SQL only when necessary.

## Execution Flow

1. Overview counts and sanity checks.
2. Develop scoped queries per show (avoid deprecated columns).
3. Generate CSV mid-reports; summarize key findings in Markdown.
4. Iterate to refine (use interim outputs to guide deeper queries).

## Commands (to be added)

- `php artisan legacy:overview`
- `php artisan legacy:show-arena-judges --show=123`
- `php artisan legacy:arena-breeds --show=123`
- `php artisan legacy:arena-dogs --show=123 [--arena=5]`
- `php artisan legacy:show-summary --show=123`
- `php artisan legacy:registrations-dogs --show=123`
- `php artisan legacy:payments-by-registration --show=123`
- `php artisan legacy:dog-payment-status --show=123`

All commands are read-only and will write CSV/MD in this folder with the same base name as the command target.

## Minimum Checklist

- [ ] `overview.md` / `overview.csv`
- [ ] `show_arena_judges.md` / `show_arena_judges.csv`
- [ ] `arena_breeds.md` / `arena_breeds.csv`
- [ ] `arena_dogs_with_details.md` / `arena_dogs_with_details.csv`
- [ ] `show_summary_by_arena.md` / `show_summary_by_arena.csv`
- [ ] `registrations_and_dogs.md` / `registrations_and_dogs.csv`
- [ ] `payments_by_registration.md` / `payments_by_registration.csv`
- [ ] `dog_payment_status.md` / `dog_payment_status.csv`
- [ ] Feature tests under `tests/Feature/LegacyData`

## Notes

- Deprecated columns are ignored per current approach (e.g., Shows_Structure.JudgeID deprecated; use
  Shows_Breeds.JudgeID).
- Use eager loading for relationships to avoid N+1.
- Use chunking for heavy result sets.
- Never mutate legacy data; scripts are read-only.
