<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevUserRequest extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'public_registration';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'club_id' => 'integer',
        'approve_1' => 'boolean',
        'approve_2' => 'boolean',
        'approve_3' => 'boolean',
        'year' => 'integer',
        'birth_date' => 'date',
        'dog1_vaccine_date' => 'date',
        'dog2_vaccine_date' => 'date',
        'dog3_vaccine_date' => 'date',
        'payment_incerments' => 'integer',
        'total_amount' => 'integer',
        'record_date_time' => 'datetime',
        'payment_date_time' => 'datetime',
        'sagirID' => 'integer',
        'shipping_type_id' => 'integer',
        'IsDone' => 'boolean',
        'DoneByUserID' => 'integer',
        'DoneDate' => 'datetime',
        'owner_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'club_id', 'id');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'sagirID', 'SagirID');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'owner_id', 'id');
    }

    public function doneBy(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'DoneByUserID', 'id');
    }
}
