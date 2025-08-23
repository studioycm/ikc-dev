<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use Illuminate\Support\Facades\DB;

class DogResultsCommand extends BaseLegacyResearchCommand
{
    protected $signature = 'legacy:dog-results {--show= : The Show ID} {--arena= : Optional Arena ID to filter}';

    protected $description = 'Report dog-to-result linkage within the same show and arena (uses shows_results.MainArenaID)';

    public function handle(): int
    {
        $showId = (int)($this->option('show') ?? 0);
        $arenaId = $this->option('arena');
        $arenaId = is_null($arenaId) ? null : (int)$arenaId;

        if ($showId <= 0) {
            $this->error('Please provide a valid --show=<ID>');
            return self::INVALID;
        }

        $show = PrevShow::on('mysql_prev')->find($showId);
        if (!$show) {
            $this->error('Show not found on mysql_prev: ID ' . $showId);
            return self::INVALID;
        }

        $this->info('Building dog-results linkage for show ID ' . $showId . '...');

        $query = DB::connection('mysql_prev')
            ->table('Shows_Dogs_DB as sd')
            ->leftJoin('shows_results as r', function ($join) {
                $join->on('r.ShowID', '=', 'sd.ShowID')
                    ->on('r.MainArenaID', '=', 'sd.ArenaID')
                    ->on('r.SagirID', '=', 'sd.SagirID');
            })
            ->where('sd.ShowID', $showId)
            ->whereNull('sd.deleted_at');

        if (!is_null($arenaId) && $arenaId > 0) {
            $query->where('sd.ArenaID', $arenaId);
        }

        $rows = $query->orderBy('sd.ArenaID')
            ->orderBy('sd.ClassID')
            ->orderBy('sd.SagirID')
            ->select([
                'sd.ShowID as show_id',
                'sd.ArenaID as arena_id',
                'sd.ClassID as class_id',
                'sd.SagirID as sagir_id',
                DB::raw('COALESCE(r.DataID, 0) as result_data_id'),
            ])->get();

        $csvPath = $this->writeCsv('dog_results.csv', [
            'show_id', 'arena_id', 'class_id', 'sagir_id', 'has_result', 'result_data_id',
        ], $rows->map(fn($r) => [
            (int)$r->show_id,
            (int)($r->arena_id ?? 0),
            (int)($r->class_id ?? 0),
            (int)$r->sagir_id,
            (int)((int)$r->result_data_id > 0 ? 1 : 0),
            (int)$r->result_data_id,
        ]));

        $md = "# Dog ↔ Results Linkage (Show + Arena)\n\n" .
            "Show: {$show->TitleName} (ID {$showId})" . (is_null($arenaId) ? '' : ", Arena {$arenaId}") . "\n\n" .
            'Generated at: ' . now()->toDateTimeString() . "\n\n" .
            "Logic: joins Shows_Dogs_DB to shows_results on ShowID and MainArenaID=ArenaID and SagirID.\n\n" .
            "See dog_results.csv for full data.\n";

        $this->writeMd('dog_results.md', $md);

        $this->info('Report generated:');
        $this->line(' - ' . $csvPath);
        $this->line(' - ' . $this->reportsPath() . DIRECTORY_SEPARATOR . 'dog_results.md');

        return self::SUCCESS;
    }
}
