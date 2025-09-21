<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class PrevUser extends Model implements HasName
{
    use SoftDeletes;
    use Notifiable;


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

    // disable fillable attributes
    protected $guarded = [];

    protected $primaryKey = 'id';

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

    public function history_dogs(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'CurrentOwnerId', 'owner_code')
            ->where('deleted_at', null);
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
            get: fn () => collect([$this->full_name, $this->mobile_phone])
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

    /* ------------- “prepared query” helper for selects ---------- */

    /** Return id => label pairs for a Select component */
    public static function selectOptions(?string $search = null, int $limit = 30): array
    {
        return static::query()
            ->searchName($search)
            ->orderByRaw("
                COALESCE(NULLIF(first_name, ''), first_name_en) ASC,
                COALESCE(NULLIF(last_name , ''), last_name_en ) ASC
            ")
            ->limit($limit)
            ->get(['id', 'first_name', 'last_name',
                'first_name_en', 'last_name_en', 'mobile_phone'])
            ->pluck('search_label', 'id')
            ->toArray();
    }

    /**
     * Clean “mobile_phone” first; if it can’t be normalised,
     * try “phone”.  Returns null when both fail.
     */
    protected function normalisedPhone(): Attribute
    {
        return Attribute::make(
            get: function () {
                $mobile = static::normaliseMsisdn($this->attributes['mobile_phone'] ?? null);

                if ($mobile !== null) {
                    return $mobile;
                }

                return static::normaliseMsisdn($this->attributes['phone'] ?? null);
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
        if ($raw === null || $raw === '') {
            return null;
        }

        // 1. keep digits only
        $digits = preg_replace('/\D+/', '', $raw);

        // 2. strip international prefixes
        $digits = preg_replace('/^(00972|972)/', '', $digits);

        // 3. guarantee a single leading zero
        $digits = ltrim($digits, '0');
        $digits = $digits === '' ? '' : '0'.$digits;

        // 4. final validation
        return preg_match('/^05\d{8}$/', $digits) ? $digits : null;
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail(): ?string
    {
        // prefer explicit email, then owner_email
        return $this->email ?: $this->owner_email ?: null;
    }

}
