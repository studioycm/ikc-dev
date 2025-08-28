<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PrevJudge extends Model
{
    protected $connection = 'mysql_prev';

    public $timestamps = false;

    protected $table = 'JudgesDB';

    // Disable Fillable Attributes
    protected $guarded = [];

    protected $primaryKey = 'DataID';

    public $incrementing = true;

    protected $casts = [
        'DataID' => 'integer',
        'BreedID' => 'integer',
        'ModificationDateTime' => 'datetime',
        'CreationDateTime' => 'datetime',
    ];

    // count show_breeds with distinct/unique RaceID
    protected function judgedBreedsAmount(): Attribute
    {
        return Attribute::make(
            get: fn(): int => $this->showBreeds()->count('RaceID')
        );
    }

    public function showBreeds(): HasMany
    {
        return $this->hasMany(PrevShowBreed::class, 'JudgeID', 'DataID');
    }

    public function judgedBreeds(): BelongsToMany
    {
        return $this->belongsToMany(
            PrevBreed::class,
            'Shows_Breeds',
            'JudgeID',
            'RaceID',
            'DataID',
            'BreedCode'
        )->distinct();
    }

    /**
     * Only breeds that actually had show dogs in the same show.
     * - Includes soft-deleted breeds (removes SoftDeletingScope from the related).
     * - Keeps return type as BelongsToMany.
     */
    public function judgedBreedsWithDogs(): BelongsToMany
    {
        return tap(
            $this->belongsToMany(
                PrevBreed::class,
                'Shows_Breeds',
                'JudgeID',
                'RaceID',
                'DataID',
                'BreedCode'
            ),
            function (BelongsToMany $relation): void {
                // Remove soft deletes scope from related BreedsDB
                $relation->getQuery()->withoutGlobalScopes([SoftDeletingScope::class]);

                // Apply the EXISTS filter (same show, matching breed, not deleted show-dog)
                $relation->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('Shows_Dogs_DB as sd')
                        ->whereColumn('sd.ShowID', 'Shows_Breeds.ShowID')
                        ->whereColumn('sd.BreedID', 'Shows_Breeds.RaceID')
                        ->whereNull('sd.deleted_at');
                });

                // Select distinct by canonical code and pick names for list rendering
                $relation->select('BreedsDB.BreedCode', 'BreedsDB.BreedName', 'BreedsDB.BreedNameEN')
                    ->distinct('BreedsDB.BreedCode');
            }
        );
    }

    protected function breedsNamesHe(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $collection = $this->relationLoaded('judgedBreedsWithDogs')
                    ? $this->judgedBreedsWithDogs
                    : $this->judgedBreedsWithDogs()->get();

                return $collection->pluck('BreedName')->filter()->unique()->sort()->join(', ');
            }
        );
    }

    protected function breedsNamesEn(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $collection = $this->relationLoaded('judgedBreedsWithDogs')
                    ? $this->judgedBreedsWithDogs
                    : $this->judgedBreedsWithDogs()->get();

                return $collection->pluck('BreedNameEN')->filter()->unique()->sort()->join(', ');
            }
        );
    }

    public function arenas(): BelongsToMany
    {
        return $this->belongsToMany(PrevShowArena::class, 'Shows_Breeds', 'JudgeID', 'ArenaID', 'DataID', 'id')
            ->using(PrevShowBreed::class)
            ->as('show_breed')
            ->withPivot('RaceID', 'OrderID', 'ArenaID');
    }
}
