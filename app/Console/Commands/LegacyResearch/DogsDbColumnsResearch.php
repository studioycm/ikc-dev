<?php

namespace App\Console\Commands\LegacyResearch;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DogsDbColumnsResearch extends BaseLegacyResearchCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'prev:dogsdb:columns-research
        {--connection=mysql_prev : Database connection to profile}
        {--since=2022-03-01 : W2 window start date (inclusive, YYYY-MM-DD)}
        {--table=DogsDB : Table to analyze}
        {--out=DogsDB_fields_analysis.csv : Output CSV filename under docs/legacy-data-research}
    ';

    /**
     * The console command description.
     */
    protected $description = 'Analyze legacy DogsDB columns with W1/W2 split and enrich with metadata (types, samples, human/semantic names, relation/enum maps).';

    /** Map of human names by column */
    private array $humanNames = [
        'Heb_Name' => 'Hebrew Name',
        'Eng_Name' => 'English Name',
        'BeitGidulID' => 'Breeding House',
        'RegDate' => 'Registration Date',
        'BirthDate' => 'Birth Date',
        'RaceID' => 'Breed Code',
        'ColorID' => 'Color Code',
        'HairID' => 'Hair Code',
        'SupplementarySign' => 'Supplementary Sign',
        'GrowerId' => 'Breeder (User)',
        'CurrentOwnerId' => 'Current Owner (legacy code)',
        'OwnershipDate' => 'Ownership Date',
        'FatherSAGIR' => 'Father SAGIR ID',
        'MotherSAGIR' => 'Mother SAGIR ID',
        'Pelvis' => 'Pelvis',
        'Notes' => 'Notes',
        'ImportNumber' => 'Import Number',
        'SCH' => 'SCH',
        'RemarkCode' => 'Remark Code',
        'GenderID' => 'Gender',
        'SizeID' => 'Size',
        'ProfileImage' => 'Profile Image',
        'GroupID' => 'Group',
        'IsMagPass' => 'MAG Pass',
        'MagDate' => 'MAG Date',
        'MagJudge' => 'MAG Judge',
        'MagPlace' => 'MAG Place',
        'DnaID' => 'DNA ID',
        'Chip' => 'Microchip',
        'GidulShowType' => 'Gidul Show Type',
        'pedigree_color' => 'Pedigree Color',
        'PedigreeNotes' => 'Pedigree Notes',
        'HealthNotes' => 'Health Notes',
        'Status' => 'Status',
        'Image2' => 'Image #2',
        'TitleName' => 'Titles (pre‑2010)',
        'Breeder_Name' => 'Breeder Name (Text)',
        'BreedID' => 'Breed ID (alt)',
        'sheger_id' => 'Sheger ID',
        'sagir_prefix' => 'SAGIR Prefix',
        'is_correct' => 'Is Correct',
        'message' => 'Message',
        'message_test' => 'Message (Test)',
        'not_relevant' => 'Not Relevant',
        'IsMagPass_2' => 'MAG 2nd Pass',
        'MagDate_2' => 'MAG 2nd Date',
        'MagJudge_2' => 'MAG 2nd Judge',
        'MagPlace_2' => 'MAG 2nd Place',
        'PedigreeNotes_2' => 'Pedigree Notes (2)',
        'Notes_2' => 'Notes (2)',
        'red_pedigree' => 'Red Pedigree',
        'Chip_2' => 'Microchip (2)',
        'Foreign_Breeder_name' => 'Foreign Breeder Name',
        'Breeding_ManagerID' => 'Breeding Manager (User)',
    ];

    /** Map of purposes by column */
    private array $purposes = [
        'Heb_Name' => 'record name',
        'Eng_Name' => 'record name',
        'RegDate' => 'date - registration',
        'BirthDate' => 'date - birth',
        'RaceID' => 'relation',
        'ColorID' => 'relation',
        'HairID' => 'relation',
        'GrowerId' => 'relation',
        'CurrentOwnerId' => 'relation',
        'OwnershipDate' => 'date',
        'FatherSAGIR' => 'relation',
        'MotherSAGIR' => 'relation',
        'DnaID' => 'identifier',
        'Chip' => 'identifier',
        'Chip_2' => 'identifier',
        'sagir_prefix' => 'enum',
        'IsMagPass' => 'flag',
        'IsMagPass_2' => 'flag',
        'MagDate' => 'date',
        'MagDate_2' => 'date',
        'not_relevant' => 'flag',
        'red_pedigree' => 'flag',
    ];

    /** Map of relation suggestions (column => Table.Column) */
    private array $relationKeys = [
        'FatherSAGIR' => 'DogsDB.SagirID',
        'MotherSAGIR' => 'DogsDB.SagirID',
        'RaceID' => 'BreedsDB.BreedCode',
        'BreedID' => 'BreedsDB.BreedCode',
        'BeitGidulID' => 'breedinghouses.GidulCode',
        'ColorID' => 'ColorsDB.OldCode',
        'HairID' => 'HairsDB.OldCode',
        'GrowerId' => 'users.id',
        'CurrentOwnerId' => 'users.owner_code',
        'Breeding_ManagerID' => 'users.id',
    ];

    public function handle(): int
    {
        $conn = (string)$this->option('connection');
        $since = (string)$this->option('since');
        $table = (string)$this->option('table');
        $out = (string)$this->option('out');

        $database = (string)(config("database.connections.$conn.database") ?? '');
        if ($database === '') {
            $this->error("Connection '$conn' has no configured database.");

            return self::FAILURE;
        }

        $allColumns = $this->fetchColumns($conn, $database, $table);
        if (empty($allColumns)) {
            $this->warn("No columns found for $table on $database.");

            return self::SUCCESS;
        }

        $wanted = $this->wantedColumns();
        $columns = array_values(array_intersect($wanted, $allColumns));

        // Date column for W1/W2 split
        $dateCol = $this->pickDateColumn($allColumns);

        $totalAll = $this->countRows($conn, $table, null, null, $since);
        $totalW1 = $dateCol ? $this->countRows($conn, $table, $dateCol, '<', $since) : null;
        $totalW2 = $dateCol ? $this->countRows($conn, $table, $dateCol, '>=', $since) : null;

        $headers = [
            'column', 'data_type', 'human_name', 'semantic', 'purpose',
            'non_null_w1', 'total_w1', 'pct_w1',
            'non_null_w2', 'total_w2', 'pct_w2',
            'non_null_total', 'total_all', 'pct_all',
            'samples', 'relation_map', 'enum_map', 'related_table', 'related_column', 'date_split_column',
        ];
        $rows = [];

        foreach ($columns as $col) {
            $type = $this->columnType($conn, $database, $table, $col);

            $nnW1 = $dateCol ? $this->countNonNull($conn, $table, $col, $dateCol, '<', $since) : null;
            $nnW2 = $dateCol ? $this->countNonNull($conn, $table, $col, $dateCol, '>=', $since) : null;
            $nnAll = $this->countNonNull($conn, $table, $col, null, null, $since);

            $pctW1 = ($dateCol && $totalW1) ? round((($nnW1 ?? 0) / max(1, $totalW1)) * 100, 2) : null;
            $pctW2 = ($dateCol && $totalW2) ? round((($nnW2 ?? 0) / max(1, $totalW2)) * 100, 2) : null;
            $pctAll = $totalAll ? round(($nnAll / max(1, $totalAll)) * 100, 2) : null;

            $samples = $this->sampleValues($conn, $table, $col);
            $human = $this->humanNames[$col] ?? $this->humanize($col);
            $purpose = $this->purposes[$col] ?? $this->inferPurpose($col, $type);
            $semantic = $this->semanticDescription($col, $type, $samples, $purpose);

            $rel = $this->relationKeys[$col] ?? '';
            [$relTable, $relColumn] = $rel ? explode('.', $rel) + [null, null] : [null, null];
            $enumMap = $col === 'sagir_prefix' ? json_encode(\App\Models\PrevDog::SAGIR_PREFIX_MAP, JSON_UNESCAPED_UNICODE) : '';

            $rows[] = [
                $col,
                $type,
                $human,
                $semantic,
                $purpose,
                $nnW1,
                $totalW1,
                $pctW1,
                $nnW2,
                $totalW2,
                $pctW2,
                $nnAll,
                $totalAll,
                $pctAll,
                implode(' | ', $samples),
                $rel,
                $enumMap,
                $relTable,
                $relColumn,
                $dateCol,
            ];
        }

        $path = $this->writeCsv($out, $headers, $rows);
        $this->info("DogsDB columns analysis written to: $path");

        return self::SUCCESS;
    }

    /** @return list<string> */
    private function wantedColumns(): array
    {
        return [
            'Heb_Name', 'Eng_Name', 'BeitGidulID', 'RegDate', 'BirthDate', 'RaceID', 'ColorID', 'HairID', 'SupplementarySign', 'GrowerId', 'CurrentOwnerId', 'OwnershipDate', 'FatherSAGIR', 'MotherSAGIR', 'Pelvis', 'Notes', 'ImportNumber', 'SCH', 'RemarkCode', 'GenderID', 'SizeID', 'ProfileImage', 'GroupID', 'IsMagPass', 'MagDate', 'MagJudge', 'MagPlace', 'DnaID', 'Chip', 'GidulShowType', 'pedigree_color', 'PedigreeNotes', 'HealthNotes', 'Status', 'Image2', 'TitleName', 'Breeder_Name', 'BreedID', 'sheger_id', 'sagir_prefix', 'is_correct', 'message', 'message_test', 'not_relevant', 'IsMagPass_2', 'MagDate_2', 'MagJudge_2', 'MagPlace_2', 'PedigreeNotes_2', 'Notes_2', 'red_pedigree', 'Chip_2', 'Foreign_Breeder_name', 'Breeding_ManagerID',
        ];
    }

    /** @return list<string> */
    private function fetchColumns(string $conn, string $database, string $table): array
    {
        $rows = DB::connection($conn)->select(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION',
            [$database, $table]
        );

        return array_map(fn($r) => $r->COLUMN_NAME, $rows);
    }

    private function pickDateColumn(array $columns): ?string
    {
        $candidates = ['created_at', 'CreationDateTime', 'RegDate', 'BirthDate'];
        foreach ($candidates as $c) {
            if (in_array($c, $columns, true)) {
                return $c;
            }
        }

        return null;
    }

    private function countRows(string $conn, string $table, ?string $dateCol, ?string $op, string $since): int
    {
        $q = DB::connection($conn)->table($table);
        if ($dateCol !== null && $op !== null) {
            $q->where($dateCol, $op, $since);
        }

        return (int)$q->count();
    }

    private function countNonNull(string $conn, string $table, string $col, ?string $dateCol, ?string $op, string $since): int
    {
        $q = DB::connection($conn)->table($table)->whereNotNull($col);
        if ($dateCol !== null && $op !== null) {
            $q->where($dateCol, $op, $since);
        }

        return (int)$q->count();
    }

    private function columnType(string $conn, string $database, string $table, string $col): string
    {
        $row = DB::connection($conn)->selectOne(
            'SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1',
            [$database, $table, $col]
        );

        return $row?->COLUMN_TYPE ?? 'unknown';
    }

    /** @return list<string> */
    private function sampleValues(string $conn, string $table, string $col, int $limit = 5): array
    {
        $rows = DB::connection($conn)->table($table)
            ->select($col)
            ->whereNotNull($col)
            ->distinct()
            ->limit($limit)
            ->pluck($col)
            ->all();

        return array_map(function ($v) {
            if (is_bool($v)) {
                return $v ? '1' : '0';
            }
            if ($v instanceof \DateTimeInterface) {
                return $v->format('Y-m-d');
            }

            return (string)$v;
        }, $rows);
    }

    private function humanize(string $col): string
    {
        return Str::of($col)->replace('_', ' ')->replace('-', ' ')->headline()->toString();
    }

    private function inferPurpose(string $col, string $type): string
    {
        $lc = strtolower($col);
        if (str_contains($lc, 'date')) {
            return 'date';
        }
        if (str_contains($lc, 'name')) {
            return 'record name';
        }
        if (str_contains($lc, 'id')) {
            return 'identifier';
        }
        if (str_contains($lc, 'notes')) {
            return 'notes';
        }
        if (str_contains($lc, 'chip')) {
            return 'identifier';
        }
        if (str_contains($lc, 'image')) {
            return 'media';
        }
        if (preg_match('/^(is|has|not_)/', $lc) === 1) {
            return 'flag';
        }

        return 'data';
    }

    /** Create a short semantic description */
    private function semanticDescription(string $col, string $type, array $samples, string $purpose): string
    {
        $sample = $samples[0] ?? '';
        $typeShort = preg_replace('/\(.*/', '', $type);

        return trim("$purpose; $typeShort; e.g. $sample");
    }
}
