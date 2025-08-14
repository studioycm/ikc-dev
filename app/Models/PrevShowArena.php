<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowArena extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Structure';

    protected $guarded = [];

    protected $casts = [
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'ClassID' => 'integer',
        'ArenaType' => 'integer',
        'GroupParentID' => 'integer',
        'OrderID' => 'integer',
        'JudgeID' => 'integer',
        'arena_date' => 'datetime',
        'OrderTime' => 'datetime',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id');
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(PrevJudge::class, 'JudgeID', 'DataID');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(PrevShowClass::class, 'ShowArenaID', 'id');
    }

    public function breeds(): HasMany
    {
        return $this->hasMany(PrevShowBreed::class, 'ArenaID', 'id')
            ->with('breed');
    }

}
