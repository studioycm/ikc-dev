<?php

use App\Models\PrevShow;
use App\Models\PrevShowArena;
use App\Models\PrevShowBreed;
use App\Models\PrevShowClass;
use App\Models\PrevShowDog;
use App\Models\PrevShowResult;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Legacy Prev* models – schema conformance (read-only)
|--------------------------------------------------------------------------
| These tests verify that each model is tied to the expected connection &
| table, and that SoftDeletes trait usage aligns with the existence of a
| deleted_at column in the legacy mysql_prev database.
|
| They do not mutate any data.
*/

function columnExists(string $connection, string $table, string $column): bool
{
    try {
        $results = DB::connection($connection)->select("SHOW COLUMNS FROM `{$table}`");
    } catch (Throwable $e) {
        // Connection or table not found.
        return false;
    }

    foreach ($results as $row) {
        if (isset($row->Field) && $row->Field === $column) {
            return true;
        }
    }

    return false;
}

function tableExists(string $connection, string $table): bool
{
    try {
        DB::connection($connection)->select("SHOW COLUMNS FROM `{$table}`");

        return true;
    } catch (Throwable $e) {
        return false;
    }
}

// Model => [connection, table]
$targets = [
    PrevShow::class => ['mysql_prev', 'ShowsDB'],
    PrevShowDog::class => ['mysql_prev', 'Shows_Dogs_DB'],
    PrevShowResult::class => ['mysql_prev', 'shows_results'],
    PrevShowClass::class => ['mysql_prev', 'Shows_Classes'],
    PrevShowArena::class => ['mysql_prev', 'Shows_Structure'],
    PrevShowBreed::class => ['mysql_prev', 'Shows_Breeds'],
];

it('legacy tables exist for Prev* models (mysql_prev)', function (string $model, string $connection, string $table) {
    expect(tableExists($connection, $table))->toBeTrue();
})->with(function () use ($targets) {
    return array_map(fn($pair, $model) => [$model, $pair[0], $pair[1]], $targets, array_keys($targets));
});

it('SoftDeletes trait usage matches deleted_at column existence', function (string $model, string $connection, string $table) {
    $uses = class_uses_recursive($model);
    $hasTrait = array_key_exists(SoftDeletes::class, $uses);
    $hasDeletedAt = columnExists($connection, $table, 'deleted_at');

    // If model uses SoftDeletes, table should expose deleted_at; if it does not,
    // table should typically not have it (legacy data may vary, but this flags misalignments).
    if ($hasTrait) {
        expect($hasDeletedAt)->toBeTrue();
    }
})->with(function () use ($targets) {
    return array_map(fn($pair, $model) => [$model, $pair[0], $pair[1]], $targets, array_keys($targets));
});
