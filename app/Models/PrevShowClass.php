<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrevShowClass extends Model
{
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Classes';

    protected $primaryKey = 'id';

    protected $casts = [
        'DataID' => 'integer',
        'Age_FromMonths' => 'integer',
        'Age_TillMonths' => 'integer',
        'ShowID' => 'integer',
        'ShowArenaID' => 'integer',
        'SpecialClassID' => 'integer',
        'OrderID' => 'integer',
        'JudgeID' => 'integer',
        'ShowRaceID' => 'integer',
        'BreedID' => 'integer',
        'Status' => 'integer',
        'GenderID' => 'integer',
        'AwardIDClass' => 'integer',
        'IsChampClass' => 'integer',
        'IsWorkingClass' => 'integer',
        'IsOpenClass' => 'integer',
        'IsVeteranClass' => 'integer',
        'IsCouplesClass' => 'integer',
        'IsZezaimClass' => 'integer',
        'IsYoungDriverClass' => 'integer',
        'IsBgidulClass' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Normalized relation names
    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id');
    }

    public function arena(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'ShowArenaID', 'id');
    }

    public function showDogs(): HasMany
    {
        return $this->hasMany(PrevShowDog::class, 'ClassID', 'id');
    }
}
