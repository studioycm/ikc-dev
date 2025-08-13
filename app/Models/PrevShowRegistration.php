<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowRegistration extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows_registration';

    public function showID(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID');
    }

    public function sagirID(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID');
    }

    public function classID(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'registered_by');
    }
}
