<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Filament\Models\Contracts\HasName;

class PrevUser extends Model implements HasName
{
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

    protected $appends = ['full_name', 'full_name_heb', 'full_name_eng', 'name'];

    // relationship with dogs

    public function dogs(): BelongsToMany
    {
        return $this->belongsToMany(PrevDog::class, 'dogs2users', 'user_id', 'SagirID', 'id', 'SagirID')
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
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullNameHeb(): Attribute
    {
        return Attribute::make(
            get: fn () => trim(implode(' ', array_filter([$this->first_name, $this->last_name])))
        );
    }

    /**
     * Get the user's full name in English.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
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
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $names = array_unique(array_filter([
                    $this->full_name_heb,
                    $this->full_name_eng
                ]));

                return !empty($names) ? implode(' | ', $names) : '<< Name Not Found >>';
            }
        );
    }

    /**
     * Get the user's primary display name.
     *
     * This accessor provides a fallback mechanism, preferring the Hebrew name,
     * then the English name, and finally a default placeholder.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->full_name_heb ?: $this->full_name_eng ?: '---'
        );
    }

    /**
     * Get the name of the user for Filament.
     *
     * @return string
     */
    public function getFilamentName(): string
    {
        return $this->name;
    }

}
