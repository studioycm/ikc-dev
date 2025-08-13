<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowDog extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Dogs_DB';

    public function sagirID(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID');
    }

    public function showID(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID');
    }

    public function arenaID(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'ArenaID');
    }

    public function showRegistrationID(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'ShowRegistrationID');
    }

    public function classID(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID');
    }

    public function ownerID(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'OwnerID');
    }

    public function newShowRegistration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'new_show_registration_id');
    }

    public function breedID(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'BreedID');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'present' => 'timestamp',
        ];
    }
}
