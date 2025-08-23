<?php

use App\Filament\Resources\PrevShowArenaResource;
use App\Filament\Resources\PrevShowBreedResource;
use App\Filament\Resources\PrevShowClassResource;
use App\Filament\Resources\PrevShowDogResource;
use App\Filament\Resources\PrevShowResultResource;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Filament Resource ->getEloquentQuery() smoke tests
|--------------------------------------------------------------------------
| These tests assert that the resources return a valid Eloquent Builder and
| that Filament can resolve the underlying model class. This helps catch
| the "Cannot use ::class on null" issue early without needing Livewire
| rendering or authentication for panels.
*/

/** @var array<class-string<Resource>> $resources */
$resources = [
    PrevShowArenaResource::class,
    PrevShowBreedResource::class,
    PrevShowClassResource::class,
    PrevShowDogResource::class,
    PrevShowResultResource::class,
];

it('resource getEloquentQuery returns a valid Builder with model',
    /**
     * @param class-string<Resource> $resourceClass
     */
    function (string $resourceClass) {
        /** @var Builder $builder */
        $builder = $resourceClass::getEloquentQuery();

        expect($builder)->toBeInstanceOf(Builder::class);

        // Ensure the builder resolves a model instance and its class.
        $model = $builder->getModel();

        // Safer: assert it's a real Eloquent model (avoids ::class on null)
        expect($model)->toBeInstanceOf(Model::class);

        // Optional: also ensure it matches the resource-declared model
        $expected = $resourceClass::getModel();
        expect(get_class($model))->toBe($expected);
    })->with($resources);
