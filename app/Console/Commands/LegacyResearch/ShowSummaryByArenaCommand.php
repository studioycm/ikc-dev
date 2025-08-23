<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use Illuminate\Support\Facades\DB;

class ShowSummaryByArenaCommand extends BaseLegacyResearchCommand
{
    protected $signature = 'legacy:show-summary {--show= : The Show ID}';

    protected $description = 'Generate show summary grouped by arena: judges, breeds, dog counts';

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

        $this->info('Building show summary by arena for show ID ' . $showId . '...');

        // Build aggregates using raw DB (read-only) on mysql_prev
        $breeds = DB::connection('mysql_prev')->table('Shows_Breeds')
            ->select('ArenaID', DB::raw('COUNT(DISTINCT RaceID) as breeds_count'), DB::raw('COUNT(DISTINCT JudgeID) as judges_count'))
            ->where('ShowID', $showId)
            ->groupBy('ArenaID');

        $dogs = DB::connection('mysql_prev')->table('Shows_Dogs_DB')
            ->select('ArenaID', DB::raw('COUNT(*) as dogs_count'))
            ->where('ShowID', $showId)
            ->whereNull('deleted_at')
            ->groupBy('ArenaID');

        $summary = DB::connection('mysql_prev')->table(DB::raw('( ' . $breeds->toSql() . ' ) as b'))
            ->mergeBindings($breeds)
            ->leftJoinSub($dogs, 'd', 'd.ArenaID', '=', 'b.ArenaID')
            ->select([
                DB::raw((string)$showId . ' as ShowID'),
                'b.ArenaID',
                DB::raw('COALESCE(b.judges_count, 0) as judges_count'),
                DB::raw('COALESCE(b.breeds_count, 0) as breeds_count'),
                DB::raw('COALESCE(d.dogs_count, 0) as dogs_count'),
            ])->orderBy('b.ArenaID')->get();

        $rows = $summary->map(fn($r) => [
            $showId,
            (int)$r->ArenaID,
            (int)$r->judges_count,
            (int)$r->breeds_count,
            (int)$r->dogs_count,
        ]);

        $csvPath = $this->writeCsv('show_summary_by_arena.csv', ['show_id', 'arena_id', 'judges_count', 'breeds_count', 'dogs_count'], $rows);

        $md = "# Show Summary by Arena\n\n" .
            "Show: {$show->TitleName} (ID {$showId})\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "See show_summary_by_arena.csv for full data.\n";

        $this->writeMd('show_summary_by_arena.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'show_summary_by_arena.md');

        return self::SUCCESS;
    }
}
