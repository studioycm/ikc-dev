<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowDog extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Dogs_DB';

    protected $casts = [
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'ArenaID' => 'integer',
        'ClassID' => 'integer',
        'ShowRegistrationID' => 'integer',
        'new_show_registration_id' => 'integer',
        'OwnerID' => 'integer',
        'BreedID' => 'integer',
        'SagirID' => 'integer',
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

    public function showClass(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID', 'id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'ShowRegistrationID');
    }

    public function newRegistration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'new_show_registration_id');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID', 'SagirID');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'BreedID', 'BreedCode');
    }

    // revers relation with PrevShowResult
    public function result(): BelongsTo
    {
        return $this->belongsTo(PrevShowResult::class, 'SagirID', 'SagirID')
            ->where('ShowID', $this->ShowID)
            ->where('MainArenaID', $this->ArenaID)
            ->where('ClassID', $this->ClassID);
    }
}
