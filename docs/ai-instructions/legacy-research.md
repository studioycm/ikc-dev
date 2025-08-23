# IKC Legacy Show Data — AI Agent Instructions

This document specifies the rules and workflow for researching the legacy show system data. Use it as a playbook. Do not
modify production code or schema while following these steps.

## Scope & Rules

- Do NOT change any PHP business logic unrelated to research or the database schema.
- You may: run read-only DB queries, add tests, create small research Artisan commands, and produce Markdown/CSV
  reports.
- Target stack: Laravel 12.21+, Filament v3.x, PHP 8.4, MySQL.
- Legacy data connection: `mysql_prev`; New app data: `mysql`.
- Prefer Eloquent/Query Builder; use raw SQL only when necessary.
- Be iterative: reuse interim CSV/MD outputs to refine further queries.

## Repository & Access

- Authoritative local repo: `studioycm/ikc-dev`. Legacy models live in `app/Models/Prev*.php` and use
  `$connection = 'mysql_prev'`.
- Legacy reference (read-only, for behavior): `studioycm/ikc-il` on GitHub. Look at controllers like
  ShowManagementController and FrontShowController to understand legacy behavior.

## Key Models (mysql_prev)

- PrevShow (ShowsDB)
- PrevShowDog (Shows_Dogs_DB)
- PrevShowArena (Shows_Structure)
- PrevShowBreed (Shows_Breeds)
- PrevShowClass (Shows_Classes)
- PrevShowResult (shows_results)
- PrevJudge (JudgesDB)
- PrevBreed (BreedsDB)
- PrevDog (DogsDB)
- PrevShowRegistration (shows_registration)
- PrevShowPayment (shows_payments_info)

Deprecated columns are ignored per current approach. Example: use Shows_Breeds.JudgeID for judges, not
Shows_Structure.JudgeID.

Important:

- shows_results uses MainArenaID for arena linkage; do not expect an ArenaID column there. Link to arenas via
  shows_results.MainArenaID -> Shows_Structure.id.
- Legacy naming: shows_results.RegDogID and shows_registrations.DogID refer back to the dog record (DogsDB). Modern
  joins typically use SagirID for identity.

## Deliverables

- Markdown & CSV reports under `docs/legacy-data-research/`:
    - overview.*
    - show_arena_judges.*
    - arena_breeds.*
    - arena_dogs_with_details.*
    - show_summary_by_arena.*
    - registrations_and_dogs.*
    - payments_by_registration.*
    - dog_payment_status.*
    - dog_results.*
    - findings_synthesis.md
- Artisan research commands under `app/Console/Commands/LegacyResearch/` to generate the reports:
    - `legacy:overview`
    - `legacy:show-arena-judges --show=<ID>`
    - `legacy:arena-breeds --show=<ID>`
    - `legacy:arena-dogs --show=<ID> [--arena=<ARENA_ID>]`
    - `legacy:show-summary --show=<ID>`
    - `legacy:registrations-dogs --show=<ID>`
    - `legacy:payments-by-registration --show=<ID>`
    - `legacy:dog-payment-status --show=<ID>`
    - `legacy:dog-results --show=<ID> [--arena=<ARENA_ID>]`
- Feature tests in `tests/Feature/LegacyData/` to validate integrity and aggregates (read-only).

## Workflow

1. Overview & Integrity: table counts, random show checks, FK-like validations.
2. Develop Queries: start scoped by show; avoid deprecated columns; prefer eager loading.
3. Mid-Reports: write CSVs for bulk; add concise MD summaries and note anomalies.
4. Consult legacy repo for intent; add notes to MD.
5. Synthesize: final MD summarizing relationships and patterns.

## Performance & Safety

- Use chunking/streams for large results.
- Index expectations: Shows_Breeds(ShowID,ArenaID,JudgeID,RaceID), Shows_Dogs_DB(
  ShowID,ArenaID,ClassID,BreedID,SagirID), shows_registration(ShowID), shows_payments_info(ShowID,ShowRegistrationID).
- Never mutate legacy data; all scripts are read-only.
