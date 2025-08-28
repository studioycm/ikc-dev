<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowRegistration extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows_registration';

    // disable fillable attributes
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'SagirID' => 'integer',
        'ClassID' => 'integer',
        'registered_by' => 'integer',
        'number_in_ring' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Normalized relations
    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'registered_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PrevShowPayment::class, 'RegistrationID');
    }

    // Legacy wrappers
    public function showID(): BelongsTo
    {
        return $this->show();
    }

    public function sagirID(): BelongsTo
    {
        return $this->dog();
    }

    public function classID(): BelongsTo
    {
        return $this->class();
    }

    public function registeredBy(): BelongsTo
    {
        return $this->owner();
    }
}
