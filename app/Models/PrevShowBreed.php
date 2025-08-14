<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrevShowBreed extends Model
{
    protected $connection = 'mysql_prev';

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Breeds';

    protected $primaryKey = 'DataID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [];

    protected $casts = [
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'ArenaID' => 'integer',
        'MainArenaID' => 'integer',
        'RaceID' => 'integer',
        'JudgeID' => 'integer',
        'OrderID' => 'integer',
        'count' => 'integer',
    ];

    // Normalized relation names
    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id');
    }

    public function arena(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'ArenaID', 'id');
    }


    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'RaceID', 'BreedCode');
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(PrevJudge::class, 'JudgeID', 'DataID');
    }

    // Legacy wrappers for backward compatibility
    public function arenaID(): BelongsTo { return $this->arena(); }
    public function showID(): BelongsTo { return $this->show(); }
    public function raceID(): BelongsTo { return $this->breed(); }
    public function judgeID(): BelongsTo { return $this->judge(); }
}
