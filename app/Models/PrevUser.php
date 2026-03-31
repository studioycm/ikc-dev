<?php

namespace App\Models;

use App\Builders\PrevUserBuilder;
use App\Enums\Legacy\LegacyUserRecordType;
use App\Services\Legacy\PrevUserService;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

#[UseEloquentBuilder(PrevUserBuilder::class)]
class PrevUser extends Model implements HasName
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $primaryKey = 'id';

    public $timestamps = true;

    // disable fillable attributes
    protected $guarded = [];

    protected $casts = [
        'owner_code' => 'integer',
        'info_id' => 'integer',
        'sagir_owner_id' => 'integer',
        'is_current_owner' => 'boolean',
        'order_id' => 'integer',
        'new_org_data_id' => 'integer',
        'club_id' => 'integer',
        'member_status' => 'integer',
        'owner_total_payment' => 'integer',
        'record_source' => 'integer',
        'record_type' => LegacyUserRecordType::class,
        'city_id' => 'integer',
        'breed_id' => 'integer',
        'beit_gidul_id' => 'integer',
    ];

    protected $appends = ['full_name', 'full_name_heb', 'full_name_eng', 'name', 'normalised_phone'];

    // relationship with dogs

    public function dogs(): BelongsToMany
    {
        return $this->belongsToMany(PrevDog::class, 'dogs2users', 'user_id', 'sagir_id', 'id', 'SagirID')
            ->withTimestamps()
            ->using(PrevUserDog::class)
            ->as('ownership')
            ->withPivot('status', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivot('deleted_at', null)
            ->wherePivot('status', 'current');
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(PrevClub::class, 'club2user', 'user_id', 'club_id', 'id', 'id')
            ->withTimestamps()
            ->using(PrevClubUser::class)
            ->as('membership')
            ->withPivot('id', 'expire_date', 'type', 'status', 'payment_status', 'forbidden', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }

    public function activeMemberships(): BelongsToMany
    {
        return $this->clubs()
            ->wherePivot('expire_date', '>=', now()->format('Y-m-d'))
            ->wherePivot('forbidden', false)
            ->WherePivot('payment_status', 1);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'prev_user_id', 'id');
    }

    public function history_dogs(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'CurrentOwnerId', 'owner_code')
            ->where('deleted_at', null);
    }

    // Breeding houses linked to this user via pivot table
    public function prevBreedingHouses(): BelongsToMany
    {
        return $this->belongsToMany(PrevBreedingHouse::class, 'breedhouses2users', 'user_id', 'breedinghouse_id', 'id', 'id')
            ->using(PrevBreedingHouseUser::class)
            ->withTimestamps();
    }

    public function breedingManagerOf(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'Breeding_ManagerID', 'id')
            ->where('deleted_at', null);
    }

    public function dogImports(): HasMany
    {
        return $this->hasMany(PrevDogImport::class, 'user_id', 'id');
    }

    public function ownerFiles(): HasMany
    {
        return $this->hasMany(PrevOwnerFile::class, 'owner_id', 'id');
    }

    public function createdPayments(): HasMany
    {
        return $this->hasMany(PrevPayment::class, 'created_by', 'id');
    }

    public function updatedPayments(): HasMany
    {
        return $this->hasMany(PrevPayment::class, 'updated_by', 'id');
    }

    public function ownedRequests(): HasMany
    {
        return $this->hasMany(PrevUserRequest::class, 'owner_id', 'id');
    }

    public function requestsByPhone(): HasMany
    {
        return $this->hasMany(PrevUserRequest::class, 'mobile_phone', 'mobile_phone');
    }

    public function completedRequests(): HasMany
    {
        return $this->hasMany(PrevUserRequest::class, 'DoneByUserID', 'id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(PrevUserActivity::class, 'UserID', 'id');
    }

    public function createdActivities(): HasMany
    {
        return $this->hasMany(PrevUserActivity::class, 'CreatedBy', 'id');
    }

    public function promotedBreeds(): BelongsToMany
    {
        return $this->belongsToMany(PrevBreed::class, 'user2breeds', 'user_id', 'breed_id')
            ->using(PrevBreedUser::class)
            ->withPivot('id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }

    public function managedClubs(): BelongsToMany
    {
        return $this->belongsToMany(PrevClub::class, 'user_club_manager', 'user_id', 'club_id')
            ->using(PrevClubManager::class)
            ->withPivot('id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(PrevSkill::class, 'users_skills', 'user_id', 'skill_id')
            ->using(PrevSkillUser::class)
            ->withPivot('id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }

    public function managedTasks(): HasMany
    {
        return $this->hasMany(PrevUserTask::class, 'manager_user_id', 'id');
    }

    public function relatedTasks(): HasMany
    {
        return $this->hasMany(PrevUserTask::class, 'related_to_user_id', 'id');
    }

    /**
     * Get the user's full name in Hebrew.
     */
    protected function fullNameHeb(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(implode(' ', array_filter([$this->first_name, $this->last_name])))
        );
    }

    /**
     * Get the user's full name in English.
     */
    protected function fullNameEng(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(implode(' ', array_filter([$this->first_name_en, $this->last_name_en])))
        );
    }

    /**
     * Get the user's combined full name.
     *
     * This accessor combines the Hebrew and English full names,
     * which is useful for comprehensive searching.
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $names = array_unique(array_filter([
                    $this->full_name_heb,
                    $this->full_name_eng,
                ]));

                return ! empty($names) ? implode(' | ', $names) : '<< Name Not Found >>';
            }
        );
    }

    /**
     * Get the user's primary display name.
     *
     * This accessor provides a fallback mechanism, preferring the Hebrew name,
     * then the English name, and finally a default placeholder.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_name_heb ?: $this->full_name_eng ?: '---'
        );
    }

    /**
     * Get the name of the user for Filament.
     */
    public function getFilamentName(): string
    {
        return $this->name;
    }

    /* ---------------- name / phone presentation ---------------- */

    // label accessor – used by Filament and anywhere else
    protected function searchLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => collect([$this->full_name, $this->normalised_phone, $this->email, "({$this->id})"])
                ->filter()
                ->join(' | ')
        );
    }

    /* ---------------- tokenised full-name search ---------------- */

    public function scopeSearchName(Builder $q, ?string $fullTerm): Builder
    {
        if ($fullTerm === null || $fullTerm === '') {
            return $q;
        }

        $tokens = preg_split('/[\s,]+/u', $fullTerm, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($tokens as $t) {
            $tLike = '%'.$t.'%';
            $q->where(function (Builder $sq) use ($tLike) {
                $sq->whereRaw("CONCAT_WS(' ', first_name, last_name) LIKE ?", [$tLike])
                    ->orWhereRaw("CONCAT_WS(' ', first_name_en, last_name_en) LIKE ?", [$tLike]);
            });
        }

        return $q;
    }

    public function scopeNativeRecords(Builder $query): Builder
    {
        return $query->where('record_type', 'Native');
    }

    public function scopeOwnerRecords(Builder $query): Builder
    {
        return $query->where('record_type', 'Owners');
    }

    public function scopeMemberRecords(Builder $query): Builder
    {
        return $query->where('record_type', 'Members');
    }

    /* ------------- “prepared query” helper for selects ---------- */

    /** Return id => label pairs for a Select component */
    public static function selectOptions(?string $search = null, int $limit = 50): array
    {
        return static::query()
            ->native()
            ->searchName($search)
            ->orderByRaw("
                COALESCE(NULLIF(first_name, ''), first_name_en) ASC,
                COALESCE(NULLIF(last_name , ''), last_name_en ) ASC
            ")
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name',
                'first_name_en', 'last_name_en', 'mobile_phone', 'email', 'phone'])
            ->pluck('search_label', 'id')
            ->toArray();
    }

    //    protected function mobile_phone(): Attribute
    //    {
    //        return Attribute::make(
    //            get: function () {
    //                $mobile = static::normaliseMsisdn($this->attributes['mobile_phone'] ?? null);
    //
    //                if ($mobile !== null) {
    //                    return $mobile;
    //                }
    //
    //                return static::normaliseMsisdn($this->attributes['phone'] ?? null);
    //            }
    //        );
    //    }

    /**
     * Clean “mobile_phone” first; if it can’t be normalised,
     * try “phone”.  Returns null when both fail.
     */
    protected function normalisedPhone(): Attribute
    {
        return Attribute::make(
            get: function () {
                $mobile = PrevUserService::normalisePhone($this->attributes['mobile_phone'] ?? null);

                if ($mobile !== null) {
                    return $mobile;
                }

                return PrevUserService::normalisePhone($this->attributes['phone'] ?? null);
            }
        );
    }

    /**
     * Utility that turns any phone-like input into
     * a 10-digit Israeli mobile number (05XXXXXXXX) or null.
     *
     * Rules (in order):
     *   1. Strip every non-digit character.
     *   2. Remove leading “00972” or “972”.
     *   3. Ensure exactly one leading “0”.
     *   4. Result must match /^05\d{8}$/.
     */
    protected static function normaliseMsisdn(?string $raw): ?string
    {
        return PrevUserService::normalisePhone($raw);
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): ?string
    {
        // prefer explicit email, then owner_email
        return $this->email ?: $this->owner_email ?: null;
    }

    // get and set address mutator and accessor laravel Attribute
    public function address(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->addressArray(),
        );
    }

    public function buildAddress(): string
    {
        $short_address = array_filter($this->addressArray(), fn($value) => !empty($value));

        return implode(', ', $short_address);
    }

    public function addressArray(): array
    {

        return [
            'city' => $this->address_city,
            'city_en' => $this->address_city_en,
            'street' => $this->address_street,
            'street_en' => $this->address_street_en,
            'street_number' => $this->address_street_number,
            'house_number' => $this->house_number,
            'zip' => $this->address_zip,
        ];
    }
}
