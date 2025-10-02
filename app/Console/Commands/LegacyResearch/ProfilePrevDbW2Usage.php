<?php

namespace App\Console\Commands\LegacyResearch;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfilePrevDbW2Usage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'prev:profile-w2-usage
        {--connection=mysql_prev : Database connection to profile}
        {--since=2022-03-01 : W2 window start date (inclusive, YYYY-MM-DD)}
        {--csv=docs/prev_all_tables_w2_usage.csv : Output CSV path}
        {--md=docs/Prev-All-Tables-W2-Usage.md : Output Markdown path}
        {--overview=docs/Prev-DB-Starter-Overview.md : Output starter overview path}
        {--limit-major=40 : Number of major tables to include in the overview}
    ';

    /**
     * The console command description.
     */
    protected $description = 'Profiles legacy mysql_prev tables for W2 (>= since) column usage and generates CSV/Markdown reports.';

    private array $notesMap = [
        // table => [column => note]
        'Shows_Dogs_DB' => [
            'BreedID' => 'Breed mapping via BreedsDB.BreedCode (non-standard code FK). Prefer ShowBreedID → Shows_Breeds.DataID for per-show link when present.',
            'SagirID' => 'Dog primary key (non-standard pk); relates to DogsDB.SagirID.',
            'ShowBreedID' => 'Optional per-show breed link to Shows_Breeds.DataID (sparse post-2022).',
            'new_show_registration_id' => 'Newer registration link; primary since 2022 (W2).',
        ],
        'Shows_Breeds' => [
            'RaceID' => 'Breed mapping via BreedsDB.BreedCode (non-standard code FK).',
            'JudgeID' => 'Assigned judge (often empty in W2).',
        ],
        'Shows_Classes' => [
            'BreedID' => 'Likely maps to BreedsDB.BreedCode; verify per show.',
            'ShowArenaID' => 'FK to Shows_Structure.id (arena).',
        ],
        'shows_results' => [
            'SagirID' => 'Dog key to DogsDB.SagirID.',
            'RegDogID' => 'Registration id (legacy mismatch exists pre-2022).',
            'BreedID' => 'Breed mapping via BreedsDB.BreedCode.',
            'SubArenaID' => 'Unused in practice (W1/W2).',
        ],
        'shows_registration' => [
            'SagirID' => 'Dog key to DogsDB.SagirID.',
            'registered_by' => 'FK to users.id (PrevUser).',
        ],
        'shows_payments_info' => [
            'SagirID' => 'Dog key to DogsDB.SagirID.',
            'RegistrationID' => 'FK to shows_registration.id.',
            'PaymentAmount' => 'Monetary amount (ILS).',
        ],
        'Shows_Structure' => [
            'JudgeID' => 'FK to JudgesDB.DataID (sparse).',
            'ArenaType' => 'Fully populated since 2022.',
        ],
        'ShowsDB' => [
            'DataID' => 'Legacy identifier, unused post-2022.',
            'CreationDateTime' => 'Legacy creation timestamp, unused post-2022.',
            'ShowType' => 'Enumerated type (see model accessor).',
        ],
        'JudgesDB' => [
            'BreedID' => 'Rarely used; not a strict FK.',
        ],
        'DogsDB' => [
            'RaceID' => 'Breed mapping via BreedsDB.BreedCode.',
        ],
    ];

    private array $dateColumnPriority = ['created_at', 'CreationDateTime', 'StartDate', 'EventDate'];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $conn = (string)$this->option('connection');
        $since = (string)$this->option('since');
        $csvPath = (string)$this->option('csv');
        $mdPath = (string)$this->option('md');
        $overviewPath = (string)$this->option('overview');
        $limitMajor = (int)$this->option('limit-major');

        $database = (string)(config("database.connections.$conn.database") ?? '');
        if ($database === '') {
            $this->error("Connection '$conn' has no configured database.");

            return self::FAILURE;
        }

        $tables = $this->fetchTables($conn, $database);
        if (empty($tables)) {
            $this->warn('No base tables found to profile.');

            return self::SUCCESS;
        }

        $rows = [];
        $summary = [];

        foreach ($tables as $table) {
            $columns = $this->fetchColumns($conn, $database, $table);
            if (empty($columns)) {
                continue;
            }

            $dateCol = $this->pickDateColumn($columns);
            $totalW2 = $this->countTotal($conn, $table, $dateCol, $since);

            $summary[] = [
                'table' => $table,
                'total_w2' => $totalW2,
                'date_filter' => $dateCol ?? '(none)',
            ];

            foreach ($columns as $col) {
                $usedCount = $this->countUsed($conn, $table, $col, $dateCol, $since);
                $usedPct = $totalW2 > 0 ? round(($usedCount / $totalW2) * 100, 2) : 0.0;
                [$grade, $should] = $this->grade($usedPct, $table, $col);
                $human = $this->humanName($col);
                $notes = $this->notesFor($table, $col);

                $rows[] = [
                    'table' => $table,
                    'column' => $col,
                    'human_name' => $human,
                    'used_count' => $usedCount,
                    'total_w2' => $totalW2,
                    'used_pct' => $usedPct,
                    'grade' => $grade,
                    'should' => $should,
                    'notes' => $notes,
                    'date_filter' => $dateCol ?? '',
                ];
            }
        }

        $this->writeCsv($csvPath, $rows);
        $this->writeMarkdown($mdPath, $since, $rows, $summary);
        $this->writeOverview($overviewPath, $since, $summary, $rows, $limitMajor);

        $this->info("W2 usage profiling completed. CSV: $csvPath | MD: $mdPath | Overview: $overviewPath");

        return self::SUCCESS;
    }

    /** @return list<string> */
    private function fetchTables(string $conn, string $database): array
    {
        $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_type = 'BASE TABLE' AND table_name NOT LIKE 'idune_508_raurotoddqetdjphslhr\\_%' ORDER BY table_name";
        $rows = DB::connection($conn)->select($sql, [$database]);

        return array_map(fn($r) => (string)$r->table_name, $rows);
    }

    /** @return list<string> */
    private function fetchColumns(string $conn, string $database, string $table): array
    {
        $sql = 'SELECT column_name FROM information_schema.columns WHERE table_schema = ? AND table_name = ? ORDER BY ordinal_position';
        $rows = DB::connection($conn)->select($sql, [$database, $table]);

        return array_map(fn($r) => (string)$r->column_name, $rows);
    }

    private function pickDateColumn(array $columns): ?string
    {
        foreach ($this->dateColumnPriority as $c) {
            if (in_array($c, $columns, true)) {
                return $c;
            }
        }

        return null;
    }

    private function countTotal(string $conn, string $table, ?string $dateCol, string $since): int
    {
        $quotedTable = $this->qi($table);
        if ($dateCol !== null) {
            $date = $this->qn($dateCol);
            $sql = "SELECT COUNT(*) AS c FROM $quotedTable WHERE $date >= ?";
            $res = DB::connection($conn)->select($sql, [$since]);
        } else {
            $sql = "SELECT COUNT(*) AS c FROM $quotedTable";
            $res = DB::connection($conn)->select($sql);
        }

        return (int)($res[0]->c ?? 0);
    }

    private function isStringy(string $conn, string $table, string $column): bool
    {
        // Inspect data_type from information_schema
        $database = (string)config("database.connections.$conn.database");
        $sql = 'SELECT data_type FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?';
        $row = DB::connection($conn)->selectOne($sql, [$database, $table, $column]);
        if ($row === null) {
            return false;
        }
        $type = strtolower((string)$row->data_type);

        return in_array($type, ['char', 'varchar', 'text', 'tinytext', 'mediumtext', 'longtext'], true);
    }

    private function countUsed(string $conn, string $table, string $column, ?string $dateCol, string $since): int
    {
        $quotedTable = $this->qi($table);
        $col = $this->qn($column);
        $where = $this->isStringy($conn, $table, $column)
            ? "$col IS NOT NULL AND $col <> ''"
            : "$col IS NOT NULL";

        if ($dateCol !== null) {
            $date = $this->qn($dateCol);
            $sql = "SELECT COUNT(*) AS c FROM $quotedTable WHERE $date >= ? AND ($where)";
            $res = DB::connection($conn)->select($sql, [$since]);
        } else {
            $sql = "SELECT COUNT(*) AS c FROM $quotedTable WHERE ($where)";
            $res = DB::connection($conn)->select($sql);
        }

        return (int)($res[0]->c ?? 0);
    }

    /**
     * Compute letter grade and should/shouldn’t recommendation.
     * Returns [grade, shouldFlag].
     */
    private function grade(float $usedPct, string $table, string $column): array
    {
        $upper = strtoupper($column);
        // Always important identifiers
        if (in_array($upper, ['ID', 'DATAID'], true) || Str::endsWith($upper, 'ID')) {
            return ['A', 'should'];
        }

        if ($usedPct >= 95.0) {
            return ['A', 'should'];
        }
        if ($usedPct >= 70.0) {
            return ['B', 'should'];
        }
        if ($usedPct >= 30.0) {
            return ['C', 'consider'];
        }
        if ($usedPct >= 5.0) {
            return ['D', 'shouldn\'t'];
        }

        return ['F', 'shouldn\'t'];
    }

    private function humanName(string $column): string
    {
        $name = str_replace(['_', '-'], ' ', $column);
        $name = preg_replace('/([a-z])([A-Z])/u', '$1 $2', $name ?? '') ?? $name;

        return Str::title(trim((string)$name));
    }

    private function notesFor(string $table, string $column): string
    {
        $note = $this->notesMap[$table][$column] ?? '';
        // Generic heuristics
        if ($note === '') {
            if (Str::endsWith($column, 'ID') && !Str::startsWith($column, 'Data')) {
                $note = 'Likely foreign key; verify relation mapping.';
            } elseif (Str::contains(Str::lower($column), 'amount')) {
                $note = 'Monetary amount.';
            } elseif (Str::contains(Str::lower($column), 'date')) {
                $note = 'Date/time semantics; check timezone and nullability.';
            }
        }

        return $note;
    }

    private function writeCsv(string $path, array $rows): void
    {
        $header = ['table', 'column', 'human_name', 'used_count', 'total_w2', 'used_pct', 'grade', 'should', 'notes', 'date_filter'];
        $csv = implode(',', $header) . "\n";
        foreach ($rows as $r) {
            $line = [
                $this->csvEscape($r['table']),
                $this->csvEscape($r['column']),
                $this->csvEscape($r['human_name']),
                (string)$r['used_count'],
                (string)$r['total_w2'],
                number_format((float)$r['used_pct'], 2, '.', ''),
                $this->csvEscape($r['grade']),
                $this->csvEscape($r['should']),
                $this->csvEscape($r['notes']),
                $this->csvEscape($r['date_filter']),
            ];
            $csv .= implode(',', $line) . "\n";
        }
        File::put(base_path($path), $csv);
    }

    private function writeMarkdown(string $path, string $since, array $rows, array $summary): void
    {
        // Group rows by table
        $byTable = [];
        foreach ($rows as $r) {
            $byTable[$r['table']][] = $r;
        }

        // Sort summary by total_w2 desc
        usort($summary, fn($a, $b) => ($b['total_w2'] <=> $a['total_w2']) ?: strcmp($a['table'], $b['table']));

        $md = "# Legacy DB — W2 Column Usage (since $since)\n\n";
        $md .= 'Generated by prev:profile-w2-usage on ' . now()->toDateTimeString() . "\n\n";
        $md .= "## Grading Legend\n- A (>=95%): critical, should\n- B (70–95%): important, should\n- C (30–70%): optional, consider\n- D (5–30%): marginal, shouldn’t\n- F (<5%): unused, shouldn’t\n\n";

        $md .= "## Table Ranking by W2 Rows\n";
        foreach ($summary as $s) {
            $md .= "- {$s['table']} — {$s['total_w2']} rows (date filter: {$s['date_filter']})\n";
        }
        $md .= "\n---\n\n";

        foreach ($byTable as $table => $list) {
            $dateFilter = $summary[array_search($table, array_column($summary, 'table'), true) ?: 0]['date_filter'] ?? '';
            $md .= "### $table (filter: $dateFilter)\n";
            $md .= "column | human name | used/total | % | grade | should | notes\n";
            $md .= "---|---|---|---:|:---:|:---:|---\n";
            foreach ($list as $r) {
                $md .= $r['column'] . ' | ' . $r['human_name'] . ' | ' . $r['used_count'] . '/' . $r['total_w2'] . ' | ' . number_format((float)$r['used_pct'], 2) . ' | ' . $r['grade'] . ' | ' . $r['should'] . ' | ' . ($r['notes'] ?? '') . "\n";
            }
            $md .= "\n";
        }

        File::put(base_path($path), $md);
    }

    private function writeOverview(string $path, string $since, array $summary, array $rows, int $limitMajor): void
    {
        // Major tables by row count desc
        usort($summary, fn($a, $b) => ($b['total_w2'] <=> $a['total_w2']) ?: strcmp($a['table'], $b['table']));
        $majors = array_slice($summary, 0, max(1, $limitMajor));
        $majorTables = array_map(fn($s) => $s['table'], $majors);

        $md = "# Legacy DB Starter Overview (W2 focus since $since)\n\n";
        $md .= "This overview lists major tables (by W2 row counts) and highlights key columns and relations.\n\n";
        foreach ($majorTables as $t) {
            $md .= "## $t\n";
            $md .= '- Approx W2 rows: ' . ((string)(collect($summary)->firstWhere('table', $t)['total_w2'] ?? 0)) . "\n";
            $md .= '- Date filter: ' . ((string)(collect($summary)->firstWhere('table', $t)['date_filter'] ?? '(none)')) . "\n";
            // pick top columns by grade/used_pct for display
            $cols = array_values(array_filter($rows, fn($r) => $r['table'] === $t));
            usort($cols, fn($a, $b) => ($b['used_pct'] <=> $a['used_pct']) ?: strcmp($a['column'], $b['column']));
            $top = array_slice($cols, 0, 10);
            foreach ($top as $r) {
                $md .= "  - {$r['column']} ({$r['human_name']}): {$r['used_count']}/{$r['total_w2']} ({$r['used_pct']}%) — {$r['grade']} {$r['should']}" . ($r['notes'] ? "; {$r['notes']}" : '') . "\n";
            }
            $md .= "\n";
        }

        $md .= "---\n\nNotes:\n- Known non-standard relations: DogsDB.RaceID → BreedsDB.BreedCode; Shows_Dogs_DB.BreedID → BreedsDB.BreedCode; Shows_Breeds.RaceID → BreedsDB.BreedCode; shows_results.BreedID → BreedsDB.BreedCode; shows_registration.registered_by → users.id.\n- If any semantics seem off, please confirm (e.g., Shows_Dogs_DB.OwnerID, results.RegDogID legacy joins).\n";

        File::put(base_path($path), $md);
    }

    private function csvEscape(string $value): string
    {
        $needsQuotes = str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n");
        $escaped = str_replace('"', '""', $value);

        return $needsQuotes ? '"' . $escaped . '"' : $escaped;
    }

    private function qi(string $identifier): string
    {
        // quote identifier with backticks, preserve dots if any (schema.table)
        $parts = explode('.', $identifier);
        $parts = array_map(fn($p) => '`' . str_replace('`', '``', $p) . '`', $parts);

        return implode('.', $parts);
    }

    private function qn(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
