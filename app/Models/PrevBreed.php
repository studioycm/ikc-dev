<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevBreed extends Model
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
    protected $table = 'BreedsDB';

    public $timestamps = true;

    // Disable Fillable Attributes
    protected $guarded = [];

    protected $casts = [
        'BreedCode' => 'integer',
    ];

    // reverse pivot relationship with PrevClub model
    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(PrevClub::class, 'breed_club', 'breed_id', 'club_id')
            ->using(PrevBreedClub::class)
            ->withPivot('id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }

    public function promoters(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'user2breeds', 'breed_id', 'user_id')
            ->using(PrevBreedUser::class)
            ->withPivot('id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }

    // dogs model (PrevDog) reverse relationship without soft deletes
    public function dogs(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'RaceID', 'BreedCode');
    }
}
