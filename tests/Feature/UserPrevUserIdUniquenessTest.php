<?php

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', ':memory:');

    DB::purge('sqlite');
    DB::reconnect('sqlite');

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });

    $migration = include base_path('database/migrations/2026_02_12_182428_add_prev_user_id_to_users_table.php');
    $migration->up();
});

it('enforces strict 1:1 mapping via unique prev_user_id', function () {
    DB::table('users')->insert([
        'name' => 'First',
        'email' => 'first@example.com',
        'password' => 'secret',
        'prev_user_id' => 1001,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(fn() => DB::table('users')->insert([
        'name' => 'Second',
        'email' => 'second@example.com',
        'password' => 'secret',
        'prev_user_id' => 1001,
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(QueryException::class);
});

it('allows multiple users with a null prev_user_id', function () {
    DB::table('users')->insert([
        'name' => 'First',
        'email' => 'first@example.com',
        'password' => 'secret',
        'prev_user_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('users')->insert([
        'name' => 'Second',
        'email' => 'second@example.com',
        'password' => 'secret',
        'prev_user_id' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    expect(DB::table('users')->count())->toBe(2);
});
