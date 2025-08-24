<?php

namespace App\Console\Commands\LegacyResearch;

use App\Models\PrevShow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SamplesCommand extends BaseLegacyResearchCommand
{
    protected $signature = 'legacy:samples {--limit=10 : Number of rows for top lists}';

    protected $description = 'Generate sample lists of Show and Arena IDs (read-only, mysql_prev)';

    public function handle(): int
    {
        $limit = (int)($this->option('limit') ?? 10);
        if ($limit <= 0) {
            $limit = 10;
        }

        $this->info('Building sample lists (top shows and arena candidates)...');

        // 1) Top shows by dog count (exclude soft-deleted)
        $topDogs = DB::connection('mysql_prev')
            ->table('Shows_Dogs_DB')
            ->select('ShowID', DB::raw('COUNT(*) as dogs'))
            ->whereNull('deleted_at')
            ->groupBy('ShowID')
            ->orderByDesc('dogs')
            ->limit($limit)
            ->get();

        $topDogsCsvRows = $topDogs->map(fn($r) => [(int)$r->ShowID, (int)$r->dogs]);
        $topDogsCsv = $this->writeCsv('sample_top_shows_by_dogs.csv', ['show_id', 'dogs'], $topDogsCsvRows);

        // 2) Top shows by payment volume
        $topPays = DB::connection('mysql_prev')
            ->table('shows_payments_info as p')
            ->join('shows_registration as sr', 'sr.id', '=', 'p.RegistrationID')
            ->whereNull('p.deleted_at')
            ->select('sr.ShowID', DB::raw('COUNT(*) as pays'), DB::raw('COALESCE(SUM(p.PaymentAmount), 0) as total_amount'))
            ->groupBy('sr.ShowID')
            ->orderByDesc('pays')
            ->orderByDesc('total_amount')
            ->limit($limit)
            ->get();

        $topPaysCsvRows = $topPays->map(fn($r) => [(int)$r->ShowID, (int)$r->pays, (int)$r->total_amount]);
        $topPaysCsv = $this->writeCsv('sample_top_shows_by_payments.csv', ['show_id', 'pays', 'total_amount'], $topPaysCsvRows);

        // 3) Intersect the two lists and compute extra metrics for each show
        $dogsMap = $topDogs->keyBy('ShowID');
        $paysMap = $topPays->keyBy('ShowID');
        $intersectIds = collect(array_values(array_intersect(array_keys($dogsMap->all()), array_keys($paysMap->all()))));

        // If there is no intersection, relax to top dogs list only
        $candidateIds = $intersectIds->isNotEmpty() ? $intersectIds : $topDogs->pluck('ShowID');

        $candidates = collect();
        foreach ($candidateIds as $sid) {
            $sid = (int)$sid;
            $dogs = (int)($dogsMap[$sid]->dogs ?? 0);
            $pays = (int)($paysMap[$sid]->pays ?? 0);
            $totalAmount = (int)($paysMap[$sid]->total_amount ?? 0);

            $arenas = (int)DB::connection('mysql_prev')->table('Shows_Structure')->where('ShowID', $sid)->count('id');
            $judges = (int)DB::connection('mysql_prev')->table('Shows_Breeds')->where('ShowID', $sid)->distinct()->count('JudgeID');
            $results = (int)DB::connection('mysql_prev')->table('shows_results')->where('ShowID', $sid)->count();

            $title = PrevShow::on('mysql_prev')->find($sid)?->TitleName ?? '';

            $candidates->push((object)[
                'ShowID' => $sid,
                'TitleName' => $title,
                'dogs' => $dogs,
                'pays' => $pays,
                'total_amount' => $totalAmount,
                'arenas' => $arenas,
                'judges' => $judges,
                'results' => $results,
            ]);
        }

        // Order candidates by dogs then payments then results
        $candidates = $candidates->sortByDesc(function ($r) {
            return [$r->dogs, $r->pays, $r->results, $r->total_amount];
        })->values();

        $candidatesCsvRows = $candidates->map(fn($r) => [
            (int)$r->ShowID,
            (string)$r->TitleName,
            (int)$r->dogs,
            (int)$r->pays,
            (int)$r->total_amount,
            (int)$r->arenas,
            (int)$r->judges,
            (int)$r->results,
        ]);
        $candidatesCsv = $this->writeCsv('sample_shows_intersection.csv', [
            'show_id', 'title', 'dogs', 'pays', 'total_amount', 'arenas', 'judges', 'results',
        ], $candidatesCsvRows);

        // 4) Pick SHOW_A and compute arena candidates within it
        $samplesRows = [];
        $pickedShowId = null;
        $pickedArenaId = null;

        if ($candidates->isNotEmpty()) {
            /** @var object $best */
            $best = $candidates->first();
            $pickedShowId = (int)$best->ShowID;
            $samplesRows[] = ['show', $pickedShowId, 'Auto-picked from intersection/top dogs & payments'];

            // Arena dogs per arena
            $arenaDogs = DB::connection('mysql_prev')
                ->table('Shows_Dogs_DB')
                ->select('ArenaID', DB::raw('COUNT(*) as dogs'))
                ->where('ShowID', $pickedShowId)
                ->whereNull('deleted_at')
                ->groupBy('ArenaID')
                ->get();
            $arenaDogsMap = $arenaDogs->keyBy('ArenaID');

            // Judges and breeds per arena (only breeds that had dogs in the same arena of the same show)
            $arenaJB = DB::connection('mysql_prev')
                ->table('Shows_Breeds as sb')
                ->select('sb.ArenaID', DB::raw('COUNT(DISTINCT sb.JudgeID) as judges'), DB::raw('COUNT(DISTINCT sb.RaceID) as breeds_with_dogs'))
                ->where('sb.ShowID', $pickedShowId)
                ->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('Shows_Dogs_DB as sd')
                        ->whereColumn('sd.ShowID', 'sb.ShowID')
                        ->whereColumn('sd.ArenaID', 'sb.ArenaID')
                        ->whereColumn('sd.BreedID', 'sb.RaceID')
                        ->whereNull('sd.deleted_at');
                })
                ->groupBy('sb.ArenaID')
                ->get();
            $arenaJBMap = $arenaJB->keyBy('ArenaID');

            // Results per arena (MainArenaID references arena id)
            $arenaRes = DB::connection('mysql_prev')
                ->table('shows_results')
                ->select('MainArenaID as ArenaID', DB::raw('COUNT(*) as results'))
                ->where('ShowID', $pickedShowId)
                ->groupBy('MainArenaID')
                ->get();
            $arenaResMap = $arenaRes->keyBy('ArenaID');

            // Merge keys (arena ids)
            /** @var Collection<int,int> $arenaIds */
            $arenaIds = collect(array_unique(array_merge(
                $arenaDogs->pluck('ArenaID')->all(),
                $arenaJB->pluck('ArenaID')->all(),
                $arenaRes->pluck('ArenaID')->all()
            )))->filter(fn($id) => !is_null($id));

            $arenaRows = $arenaIds->map(function ($aid) use ($pickedShowId, $arenaDogsMap, $arenaJBMap, $arenaResMap) {
                $dogs = (int)($arenaDogsMap[$aid]->dogs ?? 0);
                $judges = (int)($arenaJBMap[$aid]->judges ?? 0);
                $breedsWithDogs = (int)($arenaJBMap[$aid]->breeds_with_dogs ?? 0);
                $results = (int)($arenaResMap[$aid]->results ?? 0);

                return [(int)$pickedShowId, (int)$aid, $dogs, $judges, $breedsWithDogs, $results];
            })->sortByDesc(function ($row) {
                // Sort arenas: dogs desc, judges desc, breeds_with_dogs desc, results desc
                return [-$row[2], -$row[3], -$row[4], -$row[5]];
            })->values();

            if ($arenaRows->isNotEmpty()) {
                $pickedArenaId = (int)$arenaRows->first()[1];
                $samplesRows[] = ['arena', $pickedArenaId, 'Auto-picked top arena within show ' . $pickedShowId];

                $this->writeCsv('sample_arenas_for_show_' . $pickedShowId . '.csv', [
                    'show_id', 'arena_id', 'dogs', 'judges', 'breeds_with_dogs', 'results',
                ], $arenaRows);
            }
        }

        // Always write samples.csv even if no picks (may be empty apart from header)
        $samplesCsv = $this->writeCsv('samples.csv', ['kind', 'id', 'notes'], $samplesRows);

        // Write a quick MD helper
        $md = '# Samples (Show and Arena)

Generated at: ' . now()->toDateTimeString() . '

Files:
- sample_top_shows_by_dogs.csv
- sample_top_shows_by_payments.csv
- sample_shows_intersection.csv (or fallback from top dogs if intersection is empty)
- sample_arenas_for_show_' . ($pickedShowId ?? 'N') . '.csv (if a show was auto-selected)
- samples.csv (auto-picked show/arena with notes)

Notes:
- Breeds per arena are constrained to those with at least one show-dog in that arena (EXISTS filter).
- Results are linked by shows_results.MainArenaID -> Shows_Structure.id.
- This command is read-only and does not mutate any data.
';
        $this->writeMd('samples.md', $md);

        $this->info('Sample lists generated:');
        $this->line(' - ' . $topDogsCsv);
        $this->line(' - ' . $topPaysCsv);
        $this->line(' - ' . $candidatesCsv);
        $this->line(' - ' . $samplesCsv);
        if (!is_null($pickedShowId)) {
            $this->line('Auto-picked SHOW_A = ' . $pickedShowId . (is_null($pickedArenaId) ? '' : (', ARENA_A1 = ' . $pickedArenaId)));
        } else {
            $this->warn('No show was auto-picked (no candidates found). Please inspect the CSVs to choose a show.');
        }

        return self::SUCCESS;
    }
}
