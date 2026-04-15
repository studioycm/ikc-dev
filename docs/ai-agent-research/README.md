# IKC Laravel 12 + Filament 3 Deep Research Index

This folder is the AI-facing research package for the current application state before any Filament v4 / Laravel 13+
rewrite or upgrade work.

## Files in this folder

- `architecture-and-upgrade-map.md`
    - High-level app structure.
    - Where legacy `mysql_prev` behavior is hidden in Laravel / Filament / Livewire.
    - Main upgrade and rewrite risks.
- `mysql-prev-legacy-data-guide.md`
    - The most important `mysql_prev` models, tables, columns, relations, key conventions, and legacy caveats.
    - Includes a full used-table inventory derived from the `Prev*` model set and direct `mysql_prev` query hotspots.
- `filament-livewire-shield-map.md`
    - Panels, resources, pages, relation managers, widgets, Livewire components, Blade files, and Shield integration.
    - Focuses on where UI code hides legacy table / column semantics.

## Existing generated research outputs to reuse

These reports already exist and should be treated as supporting evidence:

- `docs\legacy-data-research\overview.csv`
- `docs\legacy-data-research\overview.md`
- `docs\legacy-data-research\sample_top_shows_by_dogs.csv`
- `docs\legacy-data-research\sample_top_shows_by_payments.csv`
- `docs\legacy-data-research\sample_shows_intersection.csv`
- `docs\legacy-data-research\samples.csv`
- `docs\Prev-All-Tables-Usage.md`
- `docs\Prev-All-Tables-W2-Usage.md`
- `docs\Prev-DB-Starter-Overview.md`
- `docs\Prev-Show-Ambiguities-Research.md`
- `docs\Prev-Show-Management.md`

## Important operational note

- The built-in command `php artisan prev:tables` is currently broken because
  `App\Console\Commands\LegacyResearch\BaseLegacyResearchCommand`
  does not implement `guardLegacyConnection()` even though `ListLegacyTables` and `InspectLegacyTableColumns` call it.
- Until that command is fixed, prefer:
    - Boost database schema inspection for `mysql_prev`
    - Existing reports in `docs\legacy-data-research`
    - Direct model and Filament/Livewire code inspection

## Recommended reading order for AI agents

1. `architecture-and-upgrade-map.md`
2. `mysql-prev-legacy-data-guide.md`
3. `filament-livewire-shield-map.md`
4. Supporting evidence in `docs\legacy-data-research` and `docs\Prev-All-Tables-Usage.md`
