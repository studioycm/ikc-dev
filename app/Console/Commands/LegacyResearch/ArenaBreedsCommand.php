<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use App\Models\PrevShowBreed;

class ArenaBreedsCommand extends BaseLegacyResearchCommand
{
    /** @var string */
    protected $signature = 'legacy:arena-breeds {--show= : The Show ID to report}';

    /** @var string */
    protected $description = 'Generate report of breeds per arena for a given show (mysql_prev)';

    public function handle(): int
    {
        $showId = (int)($this->option('show') ?? 0);
        if ($showId <= 0) {
            $this->error('Please provide a valid --show=<ID>');

            return self::INVALID;
        }

        $show = PrevShow::on('mysql_prev')->find($showId);
        if (!$show) {
            $this->error('Show not found on mysql_prev: ID ' . $showId);

            return self::INVALID;
        }

        $this->info('Building breeds-per-arena report for show ID ' . $showId . '...');

        $records = PrevShowBreed::query()
            ->on('mysql_prev')
            ->where('ShowID', $showId)
            ->with(['breed', 'judge'])
            ->orderBy('ArenaID')
            ->orderBy('OrderID')
            ->get(['ShowID', 'ArenaID', 'RaceID', 'JudgeID', 'OrderID']);

        $rows = $records->map(function (PrevShowBreed $r): array {
            return [
                (int)$r->ShowID,
                (int)$r->ArenaID,
                (int)$r->RaceID,
                (string)($r->breed->BreedName ?? ''),
                (string)($r->breed->BreedNameEN ?? ''),
                (int)$r->JudgeID,
                (string)($r->judge->JudgeNameHE ?? ''),
            ];
        });

        $csvPath = $this->writeCsv('arena_breeds.csv', [
            'show_id', 'arena_id', 'breed_code', 'breed_name_he', 'breed_name_en', 'judge_id', 'judge_name_he',
        ], $rows);

        $md = "# Breeds per Arena\n\n" .
            "Show: {$show->TitleName} (ID {$showId})\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "See arena_breeds.csv for the full dataset.\n";

        $this->writeMd('arena_breeds.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'arena_breeds.md');

        return self::SUCCESS;
    }
}
