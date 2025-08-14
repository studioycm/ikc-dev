<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowResult extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows_results';

    protected $primaryKey = 'DataID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [];

    protected $casts = [
        'DataID' => 'integer',
        'RegDogID' => 'integer',
        'SagirID' => 'integer',
        'ShowOrderID' => 'integer',
        'MainArenaID' => 'integer',
        'SubArenaID' => 'integer',
        'ClassID' => 'integer',
        'ShowID' => 'integer',
        'JCAC' => 'integer',
        'GCAC' => 'integer',
        'REJCAC' => 'integer',
        'REGCAC' => 'integer',
        'CW' => 'integer',
        'BJ' => 'integer',
        'ModificationDateTime' => 'datetime',
        'CreationDateTime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID');
    }

    public function mainArena(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'MainArenaID');
    }

    public function subArena(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'SubArenaID');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'RegDogID');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID');
    }
}
