<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevUserActivity extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'UserActivities';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'UserID' => 'integer',
        'CreatedBy' => 'integer',
        'CreationDateTime' => 'datetime',
        'Is_Payment' => 'boolean',
        'Is_Show' => 'boolean',
        'Is_Study' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'UserID', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'CreatedBy', 'id');
    }
}
