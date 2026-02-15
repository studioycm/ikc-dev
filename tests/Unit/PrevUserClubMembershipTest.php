<?php

use App\Models\PrevClub;
use App\Models\PrevClubUser;
use App\Models\PrevUser;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config()->set('database.connections.mysql_prev', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]);

    DB::purge('mysql_prev');
    DB::reconnect('mysql_prev');

    Schema::connection('mysql_prev')->create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::connection('mysql_prev')->create('clubs', function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::connection('mysql_prev')->create('club2user', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('user_id');
        $table->unsignedInteger('club_id');
        $table->dateTime('expire_date')->nullable();
        $table->integer('type')->nullable();
        $table->integer('status')->nullable();
        $table->integer('payment_status')->nullable();
        $table->boolean('forbidden')->default(false);
        $table->timestamps();
        $table->softDeletes();
    });
});

it('loads club memberships through club2user', function () {
    $user = PrevUser::query()->create();
    $club = PrevClub::query()->create();

    $user->clubs()->attach($club->id, [
        'expire_date' => now()->addYear(),
        'type' => 1,
        'status' => 1,
        'payment_status' => 2,
        'forbidden' => false,
    ]);

    $clubs = $user->clubs()->get();

    expect($clubs)->toHaveCount(1);
    expect($clubs->first())->toBeInstanceOf(PrevClub::class);
    expect($clubs->first()->membership)->toBeInstanceOf(PrevClubUser::class);
    expect($clubs->first()->membership->user_id)->toBe($user->id);
    expect($clubs->first()->membership->club_id)->toBe($club->id);
    expect($clubs->first()->membership->id)->not->toBeNull();

    expect($club->members()->get())->toHaveCount(1);
});

it('excludes soft-deleted club2user pivot rows', function () {
    $user = PrevUser::query()->create();
    $club = PrevClub::query()->create();

    $user->clubs()->attach($club->id);

    DB::connection('mysql_prev')
        ->table('club2user')
        ->where('user_id', $user->id)
        ->where('club_id', $club->id)
        ->update(['deleted_at' => now()]);

    expect($user->fresh()->clubs()->count())->toBe(0);
    expect($club->fresh()->members()->count())->toBe(0);
});
