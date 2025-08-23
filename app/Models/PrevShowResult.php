<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrevShowResult extends Model
{
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows_results';

    protected $primaryKey = 'DataID';

    public $incrementing = true;

    protected $casts = [
        'DataID' => 'integer',
        'RegDogID' => 'integer',
        'SagirID' => 'integer',
        'ShowOrderID' => 'integer',
        'MainArenaID' => 'integer',
        'SubArenaID' => 'integer',
        'ClassID' => 'integer',
        'ShowID' => 'integer',
        'ModificationDateTime' => 'datetime',
        'CreationDateTime' => 'datetime',
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id');
    }

    public function arena(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'MainArenaID', 'id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID', 'id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'RegDogID', 'DogId');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'BreedID', 'BreedCode');
    }
}
