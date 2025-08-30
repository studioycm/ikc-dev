<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrevShowArena extends Model
{
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Structure';

    // Disable Fillable Attributes
    protected $guarded = [];

    protected $primaryKey = 'id';

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
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id')
            ->select('id', 'TitleName', 'StartDate', 'EndDate', 'location');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(PrevShowClass::class, 'ShowArenaID', 'id');
    }

    public function show_breeds(): HasMany
    {
        return $this->hasMany(PrevShowBreed::class, 'ArenaID', 'id')
            ->orderBy('OrderID')
            ->with(['judge']);

    }

    /**
     * Distinct judges that have Show_Breeds rows in this arena.
     */
    public function judges(): BelongsToMany
    {
        return $this->belongsToMany(
            PrevJudge::class,
            'Shows_Breeds',
            'ArenaID',   // FK on pivot referencing this model
            'JudgeID',   // FK on pivot referencing related model
            'id',        // local key
            'DataID'     // related key
        )->distinct();
    }

    public function show_dogs(): HasMany
    {
        return $this->hasMany(PrevShowDog::class, 'ArenaID', 'id')
            ->orderBy('OrderID')
            ->with(['dog']);
    }
}
