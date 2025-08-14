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

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'ArenaID' => 'integer',
        'ClassID' => 'integer',
        'ShowRegistrationID' => 'integer',
        'OwnerID' => 'integer',
        'BreedID' => 'integer',
        'SagirID' => 'integer',
        'new_show_registration_id' => 'integer',
        'present' => 'datetime',
        'present_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Normalized relation names
    public function show(): BelongsTo { return $this->belongsTo(PrevShow::class, 'ShowID'); }
    public function arena(): BelongsTo { return $this->belongsTo(PrevShowArena::class, 'ArenaID'); }
    public function showClass(): BelongsTo { return $this->belongsTo(PrevShowClass::class, 'ClassID'); }
    public function registration(): BelongsTo { return $this->belongsTo(PrevShowRegistration::class, 'ShowRegistrationID'); }
    public function newRegistration(): BelongsTo { return $this->belongsTo(PrevShowRegistration::class, 'new_show_registration_id'); }
    public function dog(): BelongsTo { return $this->belongsTo(PrevDog::class, 'SagirID', 'SagirID'); }
    public function owner(): BelongsTo { return $this->belongsTo(PrevUser::class, 'O', 'id'); }
    public function breed(): BelongsTo { return $this->belongsTo(PrevBreed::class, 'BreedID', 'BreedCode'); }

    // Legacy wrappers
    public function sagirID(): BelongsTo { return $this->dog(); }
    public function showID(): BelongsTo { return $this->show(); }
    public function arenaID(): BelongsTo { return $this->arena(); }
    public function classID(): BelongsTo { return $this->showClass(); }
    public function showRegistrationID(): BelongsTo { return $this->registration(); }
    public function ownerID(): BelongsTo { return $this->owner(); }
    public function newShowRegistration(): BelongsTo { return $this->newRegistration(); }
    public function breedID(): BelongsTo { return $this->breed(); }
}
