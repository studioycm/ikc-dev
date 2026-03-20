<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevPayment extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'payment';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'amount' => 'integer',
        'club_id' => 'integer',
        'breed_id' => 'integer',
        'sagir_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'payment_date_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'club_id', 'id');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'breed_id', 'id');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'sagir_id', 'SagirID');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'updated_by', 'id');
    }
}
