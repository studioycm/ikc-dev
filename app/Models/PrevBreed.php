<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    // Disable Fillable Attributes
    protected $guarded = [];

    protected $casts = [
        'BreedCode' => 'integer',
    ];

    // relationships with PrevUser model for UserManagerID and ClubManagerID
    public function userManager(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'UserManagerID', 'id');
    }

    public function clubManager(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'ClubManagerID', 'id');
    }

    // dogs model (PrevDog) reverse relationship without soft deletes
    public function dogs(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'RaceID', 'BreedCode');
    }
}
