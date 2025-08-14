<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowClass extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Classes';

    protected $casts = [
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'ShowArenaID' => 'integer',
        'ClassID' => 'integer',
        'OrderID' => 'integer',
        'JudgeID' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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
