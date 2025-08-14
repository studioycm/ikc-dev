<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrevJudge extends Model
{
    protected $connection = 'mysql_prev';

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'JudgesDB';

    protected $primaryKey = 'DataID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [];

    protected $casts = [
        'DataID' => 'integer',
        'BreedID' => 'integer',
        'ModificationDateTime' => 'datetime',
        'CreationDateTime' => 'datetime',
    ];

    public function breeds(): HasMany
    {
        return $this->hasMany(PrevShowBreed::class, 'JudgeID', 'DataID');
    }

    public function arenas(): HasMany
    {
        return $this->hasMany(PrevShowArena::class, 'JudgeID', 'DataID');
    }

}
