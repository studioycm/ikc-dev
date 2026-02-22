<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PrevClub extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'clubs';

    // disable fillable attributes
    protected $guarded = [];

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $casts = [
        'id' => 'integer',
        'DataID' => 'integer',
        'ClubCode' => 'integer',
        'ManagerID' => 'integer',
        'RegistrationPrice' => 'integer',
        'GeneralReviewFee' => 'integer',
        'DogReviewFee' => 'integer',
        'Breed_NonReg_Price' => 'integer',
        'PerDog_NonReg_Price' => 'integer',
        'TestPrice' => 'integer',
        'status' => 'string',
    ];

    //    protected $appends = ['full_address'];

    // Pivot: clubs ↔ breeds
    public function breeds(): BelongsToMany
    {
        return $this->belongsToMany(PrevBreed::class, 'breed_club', 'club_id', 'breed_id')
            ->withoutTrashed();
    }

    public function breedsWithDogs(): Collection
    {
        return $this->breeds()->withCount('dogs')->get();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'user_club_manager', 'club_id', 'user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'club2user', 'club_id', 'user_id', 'id', 'id')
            ->withTimestamps()
            ->using(PrevClubUser::class)
            ->as('membership')
            ->withPivot('id', 'expire_date', 'type', 'status', 'payment_status', 'forbidden', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at')
            ->wherePivot('status', '=', 'active')
            ->wherePivot('expire_date', '>=', now()->format('Y-m-d'))
            ->where(function ($query) {
                $query->whereNull('club2user.payment_status') // Where 'payment_status' is NULL
                ->orWhere('club2user.payment_status', 1); // Or 'payment_status' is 1
            })
            ->orderByPivot('expire_date', 'asc');
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function () {
                $parts = array_filter([
                    $this->attributes['Address'] ?? null,
                    $this->attributes['Street'] ?? null,
                    $this->attributes['Number'] ?? null,
                ]);

                return $parts ? implode(', ', $parts) : '';
            }
        );
    }

    /**
     * Scope to add breeds_count (via withCount) and dogs_count (via selectSub).
     *
     * Usage: PrevClub::query()->withCountsForResource()->get();
     */
    public function scopeWithCountsForResource(Builder $q): Builder
    {
        // breeds_count via relation withCount
        $q = $q->withCount('breeds');

        // dogs_count via subquery joining breed_club -> BreedsDB -> DogsDB
        $connection = $this->getConnectionName();

        $dogsCountSub = DB::connection($connection)
            ->table('breed_club as bc')
            ->selectRaw('COUNT(d.SagirID)')
            ->join('BreedsDB as b', 'bc.breed_id', '=', 'b.id')
            ->leftJoin('DogsDB as d', 'd.RaceID', '=', 'b.BreedCode')
            ->whereColumn('bc.club_id', 'clubs.id')
            ->whereNull('bc.deleted_at')
            ->whereNull('d.deleted_at');

        $q->selectRaw('clubs.*')
            ->selectSub($dogsCountSub, 'dogs_count');

        return $q;
    }

    /**
     * Get per-breed dogs counts for this club (as a collection).
     * Optionally cache the result for a short time (safe because data is mostly static).
     *
     * Returns collection with fields: breed_id, BreedName, BreedNameEN, BreedCode, dogs_count
     */
    public function breedsWithDogsCount(bool $useCache = true)
    {
        $cacheKey = "club:{$this->id}:breeds_dogs_count_v1";

        $fetch = function () {
            return DB::connection($this->getConnectionName())
                ->table('breed_club as bc')
                ->selectRaw('bc.breed_id, b.BreedName, b.BreedNameEN, b.BreedCode, COUNT(d.SagirID) as dogs_count')
                ->join('BreedsDB as b', 'bc.breed_id', '=', 'b.id')
                ->leftJoin('DogsDB as d', 'd.RaceID', '=', 'b.BreedCode')
                ->where('bc.club_id', $this->id)
                ->whereNull('bc.deleted_at')
                ->whereNull('d.deleted_at')
                ->groupBy('bc.breed_id', 'b.BreedName', 'b.BreedNameEN', 'b.BreedCode')
                ->get();
        };

        if (! $useCache) {
            return $fetch();
        }

        // short cache (30s) — adjust based on your sync frequency
        return Cache::remember($cacheKey, now()->addSeconds(120), $fetch);
    }

    public function totalDogsCount(bool $useCache = true): int
    {
        return (int) $this->breedsWithDogsCount($useCache)->sum('dogs_count');
    }

    /**
     * Clear breeds/dogs counts cache for this club.
     */
    public function clearCountsCache(): void
    {
        $key = "club:{$this->id}:breeds_dogs_count_v1";
        Cache::forget($key);
    }

    /**
     * Static helper to clear cache for a list of club IDs.
     *
     * Use when multiple clubs are affected (e.g. pivot updates).
     */
    public static function clearCountsCacheForClubs(array $clubIds): void
    {
        foreach ($clubIds as $id) {
            Cache::forget("club:{$id}:breeds_dogs_count_v1");
        }
    }
}
