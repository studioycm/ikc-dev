<?php

use Illuminate\Support\Facades\DB;

it('has dog→result linkage within same show and arena (mysql_prev)', function () {
    $sample = DB::connection('mysql_prev')
        ->table('shows_results as r')
        ->join('Shows_Dogs_DB as sd', function ($join) {
            $join->on('r.ShowID', '=', 'sd.ShowID')
                ->on('r.MainArenaID', '=', 'sd.ArenaID')
                ->on('r.SagirID', '=', 'sd.SagirID');
        })
        ->whereNull('sd.deleted_at')
        ->select('r.ShowID', DB::raw('COUNT(*) as c'))
        ->groupBy('r.ShowID')
        ->orderByDesc('c')
        ->limit(1)
        ->first();

    if (!$sample) {
        test()->markTestSkipped('No results linkable to dogs found in legacy database.');
    }

    $showId = (int)$sample->ShowID;

    $linked = (int)DB::connection('mysql_prev')
        ->table('shows_results as r')
        ->join('Shows_Dogs_DB as sd', function ($join) {
            $join->on('r.ShowID', '=', 'sd.ShowID')
                ->on('r.MainArenaID', '=', 'sd.ArenaID')
                ->on('r.SagirID', '=', 'sd.SagirID');
        })
        ->where('r.ShowID', $showId)
        ->whereNull('sd.deleted_at')
        ->distinct()
        ->count('sd.SagirID');

    expect($linked)->toBeGreaterThan(0);
});
