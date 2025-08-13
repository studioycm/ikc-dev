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

    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID');
    }

    public function showClasses(): HasMany
    {
        return $this->hasMany(PrevShowClass::class, 'ShowArenaID');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'ShowID' => 'integer',
            'ClassID' => 'integer',
            'ArenaType' => 'integer',
            'GroupParentID' => 'integer',
            'OrderID' => 'integer',
            'JudgeID' => 'integer',
        ];
    }
}
