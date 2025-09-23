<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevBreedingHouse extends Model
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
    protected $table = 'breedinghouses';

    // Disable Fillable Attributes
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'GidulCode' => 'integer',
            'MegadelCode' => 'integer',
            'MisparNosaf' => 'integer',
            'status' => 'boolean',
            'recommended' => 'boolean',
            'perfect' => 'boolean',
            'recommended_from_date' => 'timestamp',
            'perfect_from_date' => 'timestamp',
        ];
    }

    protected $appends = ['name'];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                $heb = trim((string)($this->attributes['HebName'] ?? ''));
                $eng = trim((string)($this->attributes['EngName'] ?? ''));

                if ($heb !== '' && $eng !== '') {
                    return $heb . ' | ' . $eng;
                }

                return $heb !== '' ? $heb : ($eng !== '' ? $eng : '<< Name Not Found >>');
            }
        );
    }

    public function dogs(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'BeitGidulID', 'GidulCode')
            ->whereNotNull('BeitGidulID')
            ->whereNot('BeitGidulID', '=', 0);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'breedhouses2users', 'breedinghouse_id', 'user_id', 'id', 'id')
            ->withTimestamps()
            ->using(PrevBreedingHouseUser::class);
    }
}
