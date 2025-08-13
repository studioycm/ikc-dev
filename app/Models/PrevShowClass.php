<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowClass extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Classes';

    public function showArenaID(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'ShowArenaID');
    }

    public function showID(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID');
    }
}
