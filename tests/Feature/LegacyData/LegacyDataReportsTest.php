<?php

use Illuminate\Support\Facades\DB;
use function Pest\Laravel\artisan;

it('each show dog belongs to an existing show (mysql_prev)', function () {
    $invalid = DB::connection('mysql_prev')
        ->table('Shows_Dogs_DB as sd')
        ->leftJoin('ShowsDB as s', 's.id', '=', 'sd.ShowID')
        ->whereNull('sd.deleted_at')
        ->whereNull('s.id')
        ->limit(1)
        ->count();

    expect($invalid)->toBe(0);
});

it('dogs per arena sum equals total dogs for a sampled show (mysql_prev)', function () {
    $sample = DB::connection('mysql_prev')
        ->table('Shows_Dogs_DB')
        ->select('ShowID', DB::raw('COUNT(*) as c'))
        ->whereNull('deleted_at')
        ->groupBy('ShowID')
        ->orderByDesc('c')
        ->limit(1)
        ->first();

    if (!$sample) {
        test()->markTestSkipped('No show dogs found in legacy database.');
    }

    $showId = (int)$sample->ShowID;

    $total = (int)DB::connection('mysql_prev')
        ->table('Shows_Dogs_DB')
        ->where('ShowID', $showId)
        ->whereNull('deleted_at')
        ->count();

    $sumByArena = (int)DB::connection('mysql_prev')
        ->table('Shows_Dogs_DB')
        ->where('ShowID', $showId)
        ->whereNull('deleted_at')
        ->select(DB::raw('SUM(cnt) as s'))
        ->fromSub(
            DB::connection('mysql_prev')->table('Shows_Dogs_DB')
                ->select('ArenaID', DB::raw('COUNT(*) as cnt'))
                ->where('ShowID', $showId)
                ->whereNull('deleted_at')
                ->groupBy('ArenaID'),
            't'
        )
        ->value('s');

    expect($sumByArena)->toBe($total);
});

it('payment coverage is > 0 when payments exist (mysql_prev)', function () {
    $sample = DB::connection('mysql_prev')
        ->table('shows_payments_info as p')
        ->join('shows_registration as sr', 'sr.id', '=', 'p.RegistrationID')
        ->whereNull('p.deleted_at')
        ->select('sr.ShowID', DB::raw('COUNT(*) as c'))
        ->groupBy('sr.ShowID')
        ->orderByDesc('c')
        ->limit(1)
        ->first();

    if (!$sample) {
        test()->markTestSkipped('No payments found in legacy database.');
    }

    $showId = (int)$sample->ShowID;

    $dogsWithPayments = (int)DB::connection('mysql_prev')
        ->table('Shows_Dogs_DB as sd')
        ->join('shows_registration as sr', function ($join) {
            $join->on('sd.ShowRegistrationID', '=', 'sr.id')
                ->orOn('sd.new_show_registration_id', '=', 'sr.id');
        })
        ->join('shows_payments_info as p', 'p.RegistrationID', '=', 'sr.id')
        ->whereNull('sd.deleted_at')
        ->whereNull('p.deleted_at')
        ->where('sr.ShowID', $showId)
        ->distinct()
        ->count('sd.SagirID');

    expect($dogsWithPayments)->toBeGreaterThan(0);
});

it('artisan commands are registered', function () {
    $result = artisan('legacy:overview');
    expect($result)->toBe(0);
});
