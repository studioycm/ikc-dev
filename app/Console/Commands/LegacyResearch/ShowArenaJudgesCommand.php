<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use App\Models\PrevShowBreed;
use Illuminate\Support\Facades\DB;

class ShowArenaJudgesCommand extends BaseLegacyResearchCommand
{
    /** @var string */
    protected $signature = 'legacy:show-arena-judges {--show= : The Show ID to report}';

    /** @var string */
    protected $description = 'Generate report of judges per arena for a given show (mysql_prev)';

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

        $this->info('Building judges-per-arena report for show ID ' . $showId . '...');

        $records = PrevShowBreed::on('mysql_prev')
            ->where('ShowID', $showId)
            // Only count breeds that actually had at least one show dog in the same arena of the same show
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('Shows_Dogs_DB as sd')
                    ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                    ->whereColumn('sd.ArenaID', 'Shows_Breeds.ArenaID')
                    ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                    ->whereNull('sd.deleted_at');
            })
            ->select([
                'ShowID',
                'ArenaID',
                'JudgeID',
                DB::raw('COUNT(DISTINCT RaceID) as breeds_count'),
            ])
            ->groupBy(['ShowID', 'ArenaID', 'JudgeID'])
            ->orderBy('ArenaID')
            ->orderBy('JudgeID')
            ->with('judge')
            ->get();

        $rows = $records->map(function ($r): array {
            return [
                (int)$r->ShowID,
                (int)$r->ArenaID,
                (int)$r->JudgeID,
                (string)($r->judge->JudgeNameHE ?? ''),
                (string)($r->judge->JudgeNameEN ?? ''),
                (int)($r->breeds_count ?? 0),
            ];
        });

        $csvPath = $this->writeCsv('show_arena_judges.csv', [
            'show_id', 'arena_id', 'judge_id', 'judge_name_he', 'judge_name_en', 'breeds_count',
        ], $rows);

        $md = "# Judges per Arena\n\n" .
            "Show: {$show->TitleName} (ID {$showId})\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "See show_arena_judges.csv for the full dataset.\n";

        $this->writeMd('show_arena_judges.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'show_arena_judges.md');

        return self::SUCCESS;
    }
}
