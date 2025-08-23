<?php

use App\Models\PrevShow;
use App\Models\PrevShowDog;
use App\Models\PrevShowResult;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/*
|--------------------------------------------------------------------------
| Relationship type checks (no DB IO required to define relation objects)
|--------------------------------------------------------------------------
| We assert the relationship methods return the correct Relation subclasses.
| These tests instantiate the model class and call relationship methods; no
| queries are executed until the relation is resolved, so this is safe.
*/

it('PrevShowDog relationships return correct relation objects', function () {
    $m = new PrevShowDog;

    expect($m->show())->toBeInstanceOf(BelongsTo::class)
        ->and($m->arena())->toBeInstanceOf(BelongsTo::class)
        ->and($m->showClass())->toBeInstanceOf(BelongsTo::class)
        ->and($m->registration())->toBeInstanceOf(BelongsTo::class)
        ->and($m->newRegistration())->toBeInstanceOf(BelongsTo::class)
        ->and($m->dog())->toBeInstanceOf(BelongsTo::class)
        ->and($m->breed())->toBeInstanceOf(BelongsTo::class)
        ->and($m->result())->toBeInstanceOf(BelongsTo::class);
});

it('PrevShowResult relationships return correct relation objects', function () {
    $m = new PrevShowResult;

    expect($m->show())->toBeInstanceOf(BelongsTo::class)
        ->and($m->arena())->toBeInstanceOf(BelongsTo::class)
        ->and($m->class())->toBeInstanceOf(BelongsTo::class)
        ->and($m->registration())->toBeInstanceOf(BelongsTo::class)
        ->and($m->dog())->toBeInstanceOf(BelongsTo::class)
        ->and($m->breed())->toBeInstanceOf(BelongsTo::class);
});

it('PrevShow has expected hasMany relationships', function () {
    $m = new PrevShow;

    expect($m->classes())->toBeInstanceOf(HasMany::class)
        ->and($m->arenas())->toBeInstanceOf(HasMany::class)
        ->and($m->registrations())->toBeInstanceOf(HasMany::class)
        ->and($m->showDogs())->toBeInstanceOf(HasMany::class)
        ->and($m->results())->toBeInstanceOf(HasMany::class)
        ->and($m->payments())->toBeInstanceOf(HasMany::class)
        ->and($m->breeds())->toBeInstanceOf(HasMany::class);
});
