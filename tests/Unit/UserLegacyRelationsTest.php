<?php

use App\Filament\Resources\UserResource;
use App\Models\PrevUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

it('User legacy relationships return correct relation objects', function () {
    $m = new User;

    expect($m->prevUser())->toBeInstanceOf(BelongsTo::class)
        ->and($m->prevDogs())->toBeInstanceOf(BelongsToMany::class);
});

it('PrevUser reverse relationship returns correct relation object', function () {
    $m = new PrevUser;

    expect($m->user())->toBeInstanceOf(HasOne::class);
});

it('User::prevUser uses prev_user_id -> id keys', function () {
    $rel = (new User)->prevUser();

    expect($rel->getForeignKeyName())->toBe('prev_user_id')
        ->and($rel->getOwnerKeyName())->toBe('id');
});

it('User::prevDogs is keyed off prev_user_id and is filtered to current ownership', function () {
    $rel = (new User)->prevDogs();

    expect($rel->getTable())->toBe('dogs2users')
        ->and($rel->getForeignPivotKeyName())->toBe('user_id')
        ->and($rel->getRelatedPivotKeyName())->toBe('sagir_id')
        ->and($rel->getParentKeyName())->toBe('prev_user_id')
        ->and($rel->getRelatedKeyName())->toBe('SagirID');

    $wheres = $rel->getQuery()->getQuery()->wheres;

    expect(collect($wheres)->contains(fn(array $where): bool => ($where['type'] ?? null) === 'Null'
        && ($where['column'] ?? null) === 'dogs2users.deleted_at'))->toBeTrue();

    expect(collect($wheres)->contains(fn(array $where): bool => ($where['type'] ?? null) === 'Basic'
        && ($where['column'] ?? null) === 'dogs2users.status'
        && ($where['operator'] ?? null) === '='
        && ($where['value'] ?? null) === 'current'))->toBeTrue();
});

it('UserResource eager loads prevUser to avoid N+1 when listing users', function () {
    $query = UserResource::getEloquentQuery();

    expect(array_keys($query->getEagerLoads()))->toContain('prevUser');
});
