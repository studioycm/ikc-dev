<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PrevClub extends Model
{
    use SoftDeletes;

    public const CLUB_STAFF_SKILL_IDS = [5, 7, 8, 10, 11];

    public const PROMOTER_SKILL_ID = 3;

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
            ->using(PrevBreedClub::class)
            ->withPivot('id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at')
            ->withoutTrashed();
    }

    public function breedsWithDogs(): Collection
    {
        return $this->breeds()->withCount('dogs')->get();
    }

    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'user_club_manager', 'club_id', 'user_id')
            ->using(PrevClubManager::class)
            ->withPivot('id', 'created_at', 'updated_at');
    }

    public function managerLinks(): HasMany
    {
        return $this->hasMany(PrevClubManager::class, 'club_id', 'id');
    }

    public function promotersQuery(): Builder
    {
        return PrevUser::query()
            ->whereHas('promotedBreeds.clubs', fn(Builder $query): Builder => $query->where('clubs.id', $this->getKey()))
            ->with([
                'promotedBreeds' => fn($query) => $query
                    ->whereHas('clubs', fn(Builder $clubQuery): Builder => $clubQuery->where('clubs.id', $this->getKey())),
                'skills' => fn($query) => $query->where('skills.id', self::PROMOTER_SKILL_ID),
            ]);
    }

    public function promoters(): Collection
    {
        if ($this->relationLoaded('breeds')) {
            return $this->promotersFromLoadedBreeds();
        }

        return $this->promotersQuery()
            ->get()
            ->map(fn(PrevUser $promoter): PrevUser => $this->decoratePromoterUser($promoter));
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PrevPayment::class, 'club_id', 'id');
    }

    public function userRequests(): HasMany
    {
        return $this->hasMany(PrevUserRequest::class, 'club_id', 'id');
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
                $query->where('club2user.payment_status', 1); // Or 'payment_status' is 1
            })
            ->orderByPivot('expire_date', 'asc');
    }

    public function managersWithClubTitles(): Collection
    {
        $managers = $this->relationLoaded('managers')
            ? $this->managers
            : $this->managers()->with([
                'skills' => fn($query) => $query->whereIn('skills.id', self::CLUB_STAFF_SKILL_IDS),
            ])->get();

        return new Collection($managers
            ->map(function (PrevUser $manager): PrevUser {
                return $this->decorateClubUser(
                    $manager,
                    $this->managerTitleLabelsForUser($manager),
                );
            })
            ->values()
            ->all());
    }

    public function membersWithClubTitles(): Collection
    {
        $members = $this->relationLoaded('members')
            ? $this->members
            : $this->members()->with([
                'skills' => fn($query) => $query->whereIn('skills.id', [...self::CLUB_STAFF_SKILL_IDS, self::PROMOTER_SKILL_ID]),
            ])->get();

        $managerIds = $this->managersWithClubTitles()->pluck('id')->all();
        $promoterIds = $this->promoters()->pluck('id')->all();

        return new Collection($members
            ->map(function (PrevUser $member) use ($managerIds, $promoterIds): PrevUser {
                $titles = collect();

                if (in_array($member->getKey(), $managerIds, true)) {
                    $titles = $titles->merge($this->managerTitleLabelsForUser($member));
                }

                if (in_array($member->getKey(), $promoterIds, true)) {
                    $titles = $titles->merge($this->promoterTitleLabelsForUser($member));
                }

                return $this->decorateClubUser($member, $titles->unique()->values());
            })
            ->values()
            ->all());
    }

    public function memberTitleLabelsForUser(PrevUser $user): SupportCollection
    {
        $titles = collect($user->getAttribute('club_titles') ?? [])
            ->filter()
            ->values();

        if ($this->managerLinks()->where('user_id', $user->getKey())->exists()) {
            $titles = $titles->merge($this->managerTitleLabelsForUser($user));
        }

        if ($user->promotedBreeds()->whereHas('clubs', fn(Builder $query): Builder => $query->where('clubs.id', $this->getKey()))->exists()) {
            $titles = $titles->merge($this->promoterTitleLabelsForUser($user));
        }

        $titles = $titles
            ->filter()
            ->unique()
            ->values();

        $user->setAttribute('club_titles', $titles->all());
        $user->setAttribute('club_titles_text', $titles->join(', '));

        return $titles;
    }

    public function managerTitleLabelsForUser(PrevUser $user): SupportCollection
    {
        $skills = $user->relationLoaded('skills')
            ? $user->skills
            : $user->skills()->whereIn('skills.id', self::CLUB_STAFF_SKILL_IDS)->get();

        return $skills
            ->whereIn('id', self::CLUB_STAFF_SKILL_IDS)
            ->map(fn(PrevSkill $skill): ?string => $this->resolveSkillLabel($skill))
            ->filter()
            ->unique()
            ->values();
    }

    public function promoterTitleLabelsForUser(PrevUser $user): SupportCollection
    {
        $skills = $user->relationLoaded('skills')
            ? $user->skills
            : $user->skills()->where('skills.id', self::PROMOTER_SKILL_ID)->get();

        $labels = $skills
            ->where('id', self::PROMOTER_SKILL_ID)
            ->map(fn(PrevSkill $skill): ?string => $this->resolveSkillLabel($skill))
            ->filter()
            ->unique()
            ->values();

        if ($labels->isNotEmpty()) {
            return $labels;
        }

        return collect([$this->promoterDefaultTitle()])
            ->filter()
            ->values();
    }

    public function contactDirectoryRows(): SupportCollection
    {
        $managerRows = $this->managersWithClubTitles()->map(fn(PrevUser $user): array => $this->makeDirectoryRow($user, __('Manager')));

        $promoterRows = $this->promoters()->map(fn(PrevUser $user): array => $this->makeDirectoryRow($user, __('Promoter')));

        return $managerRows
            ->merge($promoterRows)
            ->unique(fn(array $row): string => implode('|', [
                (string)Arr::get($row, 'role_type'),
                (string)Arr::get($row, 'name'),
                (string)Arr::get($row, 'email'),
            ]))
            ->values();
    }

    public function emailDirectoryRows(): SupportCollection
    {
        return $this->contactDirectoryRows()
            ->filter(fn(array $row): bool => filled($row['email'] ?? null))
            ->values();
    }

    public function managerTitleSummary(): array
    {
        return $this->managersWithClubTitles()
            ->map(function (PrevUser $user): ?string {
                $titles = $user->getAttribute('club_titles_text');

                if (blank($titles)) {
                    return null;
                }

                return $user->name . ' — ' . $titles;
            })
            ->filter()
            ->values()
            ->all();
    }

    public function promoterSummary(): array
    {
        return $this->promoters()
            ->map(function (PrevUser $user): string {
                $suffix = collect([
                    $user->getAttribute('club_titles_text'),
                    $user->getAttribute('club_breeds_text'),
                ])->filter()->join(' • ');

                return filled($suffix)
                    ? $user->name . ' — ' . $suffix
                    : $user->name;
            })
            ->values()
            ->all();
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

    // get only the prices columns (RegistrationPrice, GeneralReviewFee, DogReviewFee, Breed_NonReg_Price, PerDog_NonReg_Price) values in array with key => column value (label and price)
    public function pricesArray(): array
    {
        return [
            'registration' => $this->attributes['RegistrationPrice'],
            'discount_review_main' => $this->attributes['GeneralReviewFee'],
            'discount_review_per_dog' => $this->attributes['DogReviewFee'],
            'full_review_main' => $this->attributes['Breed_NonReg_Price'],
            'full_review_per_dog' => $this->attributes['PerDog_NonReg_Price'],
            'test' => $this->attributes['TestPrice'],
        ];
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

    protected function promotersFromLoadedBreeds(): Collection
    {
        return new Collection($this->breeds
            ->flatMap(function (PrevBreed $breed): SupportCollection {
                $promoters = $breed->relationLoaded('promoters')
                    ? $breed->promoters
                    : $breed->promoters()->with([
                        'skills' => fn($query) => $query->where('skills.id', self::PROMOTER_SKILL_ID),
                    ])->get();

                return $promoters->map(fn(PrevUser $promoter): array => [
                    'breed' => $breed,
                    'user' => $promoter,
                ]);
            })
            ->groupBy(fn(array $row): int => $row['user']->getKey())
            ->map(function (SupportCollection $rows): PrevUser {
                /** @var PrevUser $user */
                $user = $rows->first()['user'];
                $breeds = $rows->pluck('breed');

                return $this->decoratePromoterUser($user, $breeds);
            })
            ->values()
            ->all());
    }

    public function decoratePromoterUser(PrevUser $user, ?SupportCollection $breeds = null): PrevUser
    {
        $clubBreeds = $breeds
            ?? ($user->relationLoaded('promotedBreeds')
                ? $user->promotedBreeds
                : $user->promotedBreeds()->whereHas('clubs', fn(Builder $query): Builder => $query->where('clubs.id', $this->getKey()))->get());

        $decoratedUser = $this->decorateClubUser($user, $this->promoterTitleLabelsForUser($user));

        $decoratedUser->setAttribute(
            'club_breeds',
            $clubBreeds
                ->pluck('BreedName')
                ->filter()
                ->unique()
                ->values()
                ->all(),
        );

        $decoratedUser->setAttribute(
            'club_breeds_text',
            collect($decoratedUser->getAttribute('club_breeds'))
                ->filter()
                ->join(', '),
        );

        return $decoratedUser;
    }

    protected function decorateClubUser(PrevUser $user, SupportCollection $titles): PrevUser
    {
        $user->setAttribute('club_titles', $titles->all());
        $user->setAttribute('club_titles_text', $titles->join(', '));

        return $user;
    }

    protected function makeDirectoryRow(PrevUser $user, string $roleType): array
    {
        return [
            'role_type' => $roleType,
            'name' => $user->name,
            'titles' => $user->getAttribute('club_titles_text'),
            'email' => $user->email,
            'mobile_phone' => $user->normalised_phone ?? $user->mobile_phone,
            'breeds' => $user->getAttribute('club_breeds_text'),
        ];
    }

    protected function promoterDefaultTitle(): ?string
    {
        static $title = null;

        if ($title === null) {
            $skill = PrevSkill::query()->find(self::PROMOTER_SKILL_ID);

            $title = $skill?->skill_name
                ?: $skill?->skill_name_en
                    ?: __('Breed Promoter');
        }

        return $title;
    }

    protected function resolveSkillLabel(PrevSkill $skill): ?string
    {
        return $skill->skill_name ?: $skill->skill_name_en;
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
