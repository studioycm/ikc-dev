<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrevShowBreed extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Breeds';

    public function arenaID(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'ArenaID');
    }

    public function mainArenaID(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'MainArenaID');
    }

    public function showID(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID');
    }

    public function raceID(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'RaceID');
    }

    public function judgeID(): BelongsTo
    {
        return $this->belongsTo(PrevJudge::class, 'JudgeID');
    }
}
