<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProfilePrevDbUsage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'prev:profile-usage
        {--connection=mysql_prev : Database connection to profile}
        {--since=2022-03-01 : W2 window start date (inclusive, YYYY-MM-DD)}
        {--csv=docs/prev_all_tables_usage.csv : Output CSV path}
        {--md=docs/Prev-All-Tables-Usage.md : Output Markdown path}
        {--limit-major=40 : Number of major tables to include in the overview section}
        {--skip-distinct=0 : If >0, skip distinct counts when table total rows exceed this number}
        {--tables= : Optional comma-separated list of table names to profile}
    ';

    /**
     * The console command description.
     */
    protected $description = 'Profiles legacy mysql_prev tables across W1 (< since), W2 (>= since), and ALL-time, adding human names, descriptions, grades (1–10), and researched notes.';

    private array $notesMap = [
        'Shows_Dogs_DB' => [
            'BreedID' => 'Breed mapping via BreedsDB.BreedCode (non-standard code FK). Prefer ShowBreedID → Shows_Breeds.DataID for per-show link when present.',
            'SagirID' => 'Dog primary key (non-standard pk); relates to DogsDB.SagirID.',
            'ShowBreedID' => 'Optional per-show breed link to Shows_Breeds.DataID (sparse post‑2022).',
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
            'RegDogID' => 'Registration id (legacy mismatch exists pre‑2022).',
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
            'DataID' => 'Legacy identifier, unused post‑2022.',
            'CreationDateTime' => 'Legacy creation timestamp, unused post‑2022.',
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

    public function handle(): int
    {
        $conn = (string)$this->option('connection');
        $since = (string)$this->option('since');
        $csvPath = (string)$this->option('csv');
        $mdPath = (string)$this->option('md');
        $limitMajor = (int)$this->option('limit-major');
        $skipDistinctThreshold = (int)$this->option('skip-distinct');

        $database = (string)(config("database.connections.$conn.database") ?? '');
        if ($database === '') {
            $this->error("Connection '$conn' has no configured database.");

            return self::FAILURE;
        }

        $tables = $this->fetchTables($conn, $database);

        // Optional subset of tables
        $tablesOption = (string)($this->option('tables') ?? '');
        if (trim($tablesOption) !== '') {
            $requested = array_values(array_filter(array_map('trim', explode(',', $tablesOption)), fn($t) => $t !== ''));
            if (!empty($requested)) {
                $reqLower = array_map('strtolower', $requested);
                $tables = array_values(array_filter($tables, fn($t) => in_array(strtolower($t), $reqLower, true)));
            }
        }

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

            // Totals by window
            $totalAll = $this->countTotalAll($conn, $table);
            $totalW1 = $this->countTotalWindow($conn, $table, $dateCol, $since, '<');
            $totalW2 = $this->countTotalWindow($conn, $table, $dateCol, $since, '>=');

            $summary[] = [
                'table' => $table,
                'total_all' => $totalAll,
                'total_w1' => $totalW1,
                'total_w2' => $totalW2,
                'date_filter' => $dateCol ?? '(none)',
            ];

            foreach ($columns as $col) {
                // Metadata
                $meta = $this->columnMeta($conn, $database, $table, $col);
                $dataType = $meta['data_type'] ?? '';
                $isNullable = $meta['is_nullable'] ?? '';
                $charLen = $meta['char_len'] ?? null;

                // Used counts per window
                $usedAll = $this->countUsedAll($conn, $table, $col);
                $usedW1 = $this->countUsedWindow($conn, $table, $col, $dateCol, $since, '<');
                $usedW2 = $this->countUsedWindow($conn, $table, $col, $dateCol, $since, '>=');

                $pctAll = $totalAll > 0 ? round(($usedAll / $totalAll) * 100, 2) : 0.0;
                $pctW1 = $totalW1 > 0 ? round(($usedW1 / $totalW1) * 100, 2) : 0.0;
                $pctW2 = $totalW2 > 0 ? round(($usedW2 / $totalW2) * 100, 2) : 0.0;

                $gradeAll = $this->grade10($pctAll, $table, $col);
                $gradeW1 = $this->grade10($pctW1, $table, $col);
                $gradeW2 = $this->grade10($pctW2, $table, $col);

                // Distinct (ALL), optionally skip if table too large
                $distinctAll = null;
                if ($skipDistinctThreshold <= 0 || $totalAll <= $skipDistinctThreshold) {
                    $distinctAll = $this->countDistinctNonEmptyAll($conn, $table, $col);
                }

                $human = $this->humanName($col);
                [$shortDesc, $longDesc] = $this->descriptions($table, $col, $dataType, $isNullable, $charLen);
                $notes = $this->notesFor($table, $col, $dataType, $isNullable, $charLen, $distinctAll);

                $rows[] = [
                    'table' => $table,
                    'column' => $col,
                    'human_name' => $human,
                    'data_type' => $dataType,
                    'is_nullable' => $isNullable,
                    'char_length' => $charLen,
                    'used_w1' => $usedW1,
                    'total_w1' => $totalW1,
                    'pct_w1' => $pctW1,
                    'grade_w1' => $gradeW1,
                    'used_w2' => $usedW2,
                    'total_w2' => $totalW2,
                    'pct_w2' => $pctW2,
                    'grade_w2' => $gradeW2,
                    'used_all' => $usedAll,
                    'total_all' => $totalAll,
                    'pct_all' => $pctAll,
                    'grade_all' => $gradeAll,
                    'distinct_non_empty_all' => $distinctAll,
                    'description_short' => $shortDesc,
                    'description_long' => $longDesc,
                    'date_filter' => $dateCol ?? '',
                    'notes' => $notes,
                ];
            }
        }

        $this->writeCsv($csvPath, $rows);
        $this->writeMarkdown($mdPath, $since, $rows, $summary, $limitMajor);

        $this->info("Usage profiling (ALL/W1/W2) completed. CSV: $csvPath | MD: $mdPath");

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

    private function isStringy(string $conn, string $table, string $column): bool
    {
        $database = (string)config("database.connections.$conn.database");
        $sql = 'SELECT data_type FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?';
        $row = DB::connection($conn)->selectOne($sql, [$database, $table, $column]);
        if ($row === null) {
            return false;
        }
        $type = strtolower((string)$row->data_type);

        return in_array($type, ['char', 'varchar', 'text', 'tinytext', 'mediumtext', 'longtext'], true);
    }

    private function columnMeta(string $conn, string $database, string $table, string $column): array
    {
        $sql = 'SELECT data_type, is_nullable, character_maximum_length, numeric_precision, numeric_scale FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?';
        $row = DB::connection($conn)->selectOne($sql, [$database, $table, $column]);
        if (!$row) {
            return [];
        }

        return [
            'data_type' => (string)$row->data_type,
            'is_nullable' => (string)$row->is_nullable,
            'char_len' => $row->character_maximum_length !== null ? (int)$row->character_maximum_length : null,
            'num_precision' => $row->numeric_precision !== null ? (int)$row->numeric_precision : null,
            'num_scale' => $row->numeric_scale !== null ? (int)$row->numeric_scale : null,
        ];
    }

    private function countTotalAll(string $conn, string $table): int
    {
        $quoted = $this->qi($table);
        $sql = "SELECT COUNT(*) AS c FROM $quoted";
        $res = DB::connection($conn)->select($sql);

        return (int)($res[0]->c ?? 0);
    }

    private function countTotalWindow(string $conn, string $table, ?string $dateCol, string $since, string $op): int
    {
        $quoted = $this->qi($table);
        if ($dateCol === null) {
            // No date filter available; treat as ALL for both windows
            return $this->countTotalAll($conn, $table);
        }
        $date = $this->qn($dateCol);
        $sql = "SELECT COUNT(*) AS c FROM $quoted WHERE $date $op ?";
        $res = DB::connection($conn)->select($sql, [$since]);

        return (int)($res[0]->c ?? 0);
    }

    private function countUsedAll(string $conn, string $table, string $column): int
    {
        $quoted = $this->qi($table);
        $col = $this->qn($column);
        $where = $this->isStringy($conn, $table, $column)
            ? "$col IS NOT NULL AND $col <> ''"
            : "$col IS NOT NULL";
        $sql = "SELECT COUNT(*) AS c FROM $quoted WHERE ($where)";
        $res = DB::connection($conn)->select($sql);

        return (int)($res[0]->c ?? 0);
    }

    private function countUsedWindow(string $conn, string $table, string $column, ?string $dateCol, string $since, string $op): int
    {
        $quoted = $this->qi($table);
        $col = $this->qn($column);
        $where = $this->isStringy($conn, $table, $column)
            ? "$col IS NOT NULL AND $col <> ''"
            : "$col IS NOT NULL";
        if ($dateCol === null) {
            // No date column: same as ALL
            $sql = "SELECT COUNT(*) AS c FROM $quoted WHERE ($where)";
            $res = DB::connection($conn)->select($sql);

            return (int)($res[0]->c ?? 0);
        }
        $date = $this->qn($dateCol);
        $sql = "SELECT COUNT(*) AS c FROM $quoted WHERE $date $op ? AND ($where)";
        $res = DB::connection($conn)->select($sql, [$since]);

        return (int)($res[0]->c ?? 0);
    }

    private function countDistinctNonEmptyAll(string $conn, string $table, string $column): int
    {
        $quoted = $this->qi($table);
        $col = $this->qn($column);
        $where = "$col IS NOT NULL";
        $sql = "SELECT COUNT(DISTINCT $col) AS c FROM $quoted WHERE $where";
        $res = DB::connection($conn)->select($sql);

        return (int)($res[0]->c ?? 0);
    }

    private function humanName(string $column): string
    {
        // Split camelCase and underscores
        $name = str_replace(['_', '-'], ' ', $column);
        $name = preg_replace('/([a-z])([A-Z])/u', '$1 $2', $name ?? '') ?? $name;

        // Common abbreviations -> full English
        $replacements = [
            '/\bEN\b/i' => 'English',
            '/\bHE\b/i' => 'Hebrew',
            '/\bHeb\b/i' => 'Hebrew',
            '/\bEng\b/i' => 'English',
            '/\bID\b/' => 'Id',
            '/\bDOB\b/i' => 'Date Of Birth',
        ];
        foreach ($replacements as $pattern => $replacement) {
            $name = preg_replace($pattern, $replacement, $name) ?? $name;
        }

        return Str::title(trim((string)$name));
    }

    /**
     * Generate short and long descriptions using heuristics.
     *
     * @return array{0:string,1:string}
     */
    private function descriptions(string $table, string $column, string $dataType, string $isNullable, ?int $charLen): array
    {
        $colLower = strtolower($column);
        $short = '';
        $long = '';

        if (in_array($colLower, ['created_at', 'creationdatetime'], true)) {
            $short = 'Creation timestamp of the record.';
            $long = 'Date/time the row was created. Used for windowing (W1/W2) when available. Ensure timezone consistency and non-nullability assumptions.';
        } elseif (in_array($colLower, ['updated_at', 'modificationdatetime'], true)) {
            $short = 'Last modification timestamp.';
            $long = 'Date/time when the row was last updated. Often nullable; not used for windowing. Useful for audit trails but not a presence indicator.';
        } elseif ($colLower === 'deleted_at') {
            $short = 'Soft delete timestamp.';
            $long = 'Nullable timestamp set when the row is soft-deleted. Presence here indicates deletion; absence does not imply use.';
        } elseif (str_ends_with(strtolower($column), 'id')) {
            $short = 'Identifier or foreign key reference.';
            $long = 'Integer-like column used as a primary key or a foreign key to a related table. Validate referential integrity; in legacy DB, many FKs are implicit (e.g., BreedCode, SagirID).';
        } elseif (str_contains($colLower, 'name')) {
            $short = 'Name or label text.';
            $long = 'Human-readable name field. For bilingual columns (Hebrew/English), consider fallbacks and presentation rules in UI.';
        } elseif (str_contains($colLower, 'amount') || str_contains($colLower, 'price')) {
            $short = 'Monetary amount.';
            $long = 'Numeric amount (usually ILS). Verify decimal scale/precision and casting to float in Laravel models to avoid rounding issues.';
        } elseif (str_contains($colLower, 'date') || str_contains($colLower, 'time')) {
            $short = 'Date/time value.';
            $long = 'Temporal field; confirm timezone and valid ranges. In legacy data some fields may be sparsely populated.';
        } else {
            $short = 'Data field from legacy system.';
            $long = 'Legacy column. See notes for mapping assumptions and quality. Use the usage percentages and grades to decide migration or modeling priority.';
        }

        // Append type/nullability
        $typeInfo = " Type: $dataType" . ($charLen !== null ? "($charLen)" : '') . ", Nullable: $isNullable.";
        $long .= $typeInfo;

        return [$short, $long];
    }

    private function notesFor(string $table, string $column, string $dataType, string $isNullable, ?int $charLen, ?int $distinctAll): string
    {
        $base = $this->notesMap[$table][$column] ?? '';
        $pieces = [];
        if ($base !== '') {
            $pieces[] = $base;
        }
        // Enrich notes with metadata
        $pieces[] = 'Data type: ' . $dataType . ($charLen !== null ? "($charLen)" : '') . ', Nullable: ' . $isNullable . '.';
        if ($distinctAll !== null) {
            $pieces[] = 'Distinct (all-time, non-null): ' . $distinctAll;
        }

        // Heuristics for common columns
        $cl = strtolower($column);
        if ($cl === 'deleted_at') {
            $pieces[] = 'Soft-deletion flag; low usage does not imply irrelevance.';
        }
        if ($cl === 'created_at' || $cl === 'creationdatetime') {
            $pieces[] = 'Primary candidate for windowing (W1/W2) if populated; otherwise fallback to other date columns.';
        }
        if (str_ends_with($cl, 'id') && !str_starts_with($cl, 'data')) {
            $pieces[] = 'Likely FK; confirm target table via relationship map.';
        }

        return implode(' ', $pieces);
    }

    private function probabilityScoreBase(float $pct): float
    {
        // Convert percentage into a 0–10 scale using an exponential curve that rewards early gains
        // scoreRaw = 10 * (1 - exp(-4 * p)), where p in [0,1]
        $p = max(0.0, min(1.0, $pct / 100.0));
        $score = 10.0 * (1.0 - exp(-4.0 * $p));

        return $score;
    }

    private function grade10(float $pct, string $table, string $column): int
    {
        $score = $this->probabilityScoreBase($pct);
        $cl = strtolower($column);

        // Heuristic boosts/penalties
        if (preg_match('/(^|_)id$/i', $column) || str_ends_with($cl, 'id')) {
            $score += 0.8; // IDs are important even at moderate usage
        }
        if ($cl === 'deleted_at') {
            $score -= 1.5; // soft-delete presence is not direct evidence of business use
        }
        if ($cl === 'updated_at') {
            $score -= 0.5;
        }
        if (str_contains($cl, 'amount') || str_contains($cl, 'price')) {
            $score += 0.5;
        }
        if ($cl === 'created_at' || $cl === 'creationdatetime') {
            $score += 0.3;
        }

        $score = max(1.0, min(10.0, $score));

        return (int)round($score);
    }

    private function writeCsv(string $path, array $rows): void
    {
        $header = [
            'table', 'column', 'human_name', 'data_type', 'is_nullable', 'char_length',
            'used_w1', 'total_w1', 'pct_w1', 'grade_w1',
            'used_w2', 'total_w2', 'pct_w2', 'grade_w2',
            'used_all', 'total_all', 'pct_all', 'grade_all',
            'distinct_non_empty_all', 'description_short', 'description_long', 'date_filter', 'notes',
        ];
        $csv = implode(',', $header) . "\n";
        foreach ($rows as $r) {
            $line = [
                $this->csvEscape((string)$r['table']),
                $this->csvEscape((string)$r['column']),
                $this->csvEscape((string)$r['human_name']),
                $this->csvEscape((string)$r['data_type']),
                $this->csvEscape((string)$r['is_nullable']),
                $r['char_length'] === null ? '' : (string)$r['char_length'],
                (string)$r['used_w1'], (string)$r['total_w1'], number_format((float)$r['pct_w1'], 2, '.', ''), (string)$r['grade_w1'],
                (string)$r['used_w2'], (string)$r['total_w2'], number_format((float)$r['pct_w2'], 2, '.', ''), (string)$r['grade_w2'],
                (string)$r['used_all'], (string)$r['total_all'], number_format((float)$r['pct_all'], 2, '.', ''), (string)$r['grade_all'],
                $r['distinct_non_empty_all'] === null ? '' : (string)$r['distinct_non_empty_all'],
                $this->csvEscape((string)$r['description_short']),
                $this->csvEscape((string)$r['description_long']),
                $this->csvEscape((string)$r['date_filter']),
                $this->csvEscape((string)$r['notes']),
            ];
            $csv .= implode(',', $line) . "\n";
        }
        File::put(base_path($path), $csv);
    }

    private function writeMarkdown(string $path, string $since, array $rows, array $summary, int $limitMajor): void
    {
        // Group rows by table
        $byTable = [];
        foreach ($rows as $r) {
            $byTable[$r['table']][] = $r;
        }

        // Sort summary by ALL rows desc
        usort($summary, fn($a, $b) => ($b['total_all'] <=> $a['total_all']) ?: strcmp($a['table'], $b['table']));

        $md = "# Legacy DB — Column Usage (All Time, W1, W2)\n\n";
        $md .= 'Generated by prev:profile-usage on ' . now()->toDateTimeString() . "\n\n";
        $md .= "## Windows\n- W1: Rows where date column < $since (Before W2)\n- W2: Rows where date column ≥ $since (From March 2022)\n- ALL: All records (no date filter)\n\n";
        $md .= "Date filtering rule: prefer created_at, then CreationDateTime, then StartDate/EventDate if present. If no date column exists, W1/W2 totals match ALL.\n\n";

        $md .= "## Grading (1–10)\n";
        $md .= "We estimate the probability a column is actively used by transforming its usage percentage with an exponential curve: score = round(10 * (1 - exp(-4 * p))), p = used_pct/100.\n";
        $md .= "Small heuristic adjustments are applied for common semantics (IDs +0.8, monetary +0.5, updated_at -0.5, deleted_at -1.5, created_at +0.3).\n\n";

        $md .= "## Major Tables by All-Time Rows\n";
        foreach (array_slice($summary, 0, max(1, $limitMajor)) as $s) {
            $md .= "- {$s['table']} — ALL: {$s['total_all']} rows; W1: {$s['total_w1']}; W2: {$s['total_w2']} (date filter: {$s['date_filter']})\n";
        }
        $md .= "\n---\n\n";

        foreach ($byTable as $table => $list) {
            $info = collect($summary)->firstWhere('table', $table);
            $dateFilter = $info['date_filter'] ?? '';
            $md .= "### $table (filter: $dateFilter)\n";
            $md .= "column | human name | W1 used/total | W1 % | grade | W2 used/total | W2 % | grade | ALL used/total | ALL % | grade | type | nullable | notes\n";
            $md .= "---|---|---|---:|:---:|---|---:|:---:|---|---:|:---:|---|:---:|---\n";
            foreach ($list as $r) {
                $md .= $r['column'] . ' | ' . $r['human_name'] . ' | ' .
                    $r['used_w1'] . '/' . $r['total_w1'] . ' | ' . number_format((float)$r['pct_w1'], 2) . ' | ' . $r['grade_w1'] . ' | ' .
                    $r['used_w2'] . '/' . $r['total_w2'] . ' | ' . number_format((float)$r['pct_w2'], 2) . ' | ' . $r['grade_w2'] . ' | ' .
                    $r['used_all'] . '/' . $r['total_all'] . ' | ' . number_format((float)$r['pct_all'], 2) . ' | ' . $r['grade_all'] . ' | ' .
                    $r['data_type'] . ($r['char_length'] ? '(' . $r['char_length'] . ')' : '') . ' | ' . $r['is_nullable'] . ' | ' . ($r['notes'] ?? '') . "\n";
            }
            $md .= "\n";
        }

        $md .= "---\n\nPrepared by: IKC Dev — Legacy Data Profiling\n";
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
        $parts = explode('.', $identifier);
        $parts = array_map(fn($p) => '`' . str_replace('`', '``', $p) . '`', $parts);

        return implode('.', $parts);
    }

    private function qn(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
