<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevUserTask extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'users_tasks';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'manager_user_id' => 'integer',
        'related_to_user_id' => 'integer',
        'due_date_time' => 'datetime',
        'read_status' => 'boolean',
        'related_breeding_process_id' => 'integer',
        'is_editable' => 'boolean',
        'done_date_time' => 'datetime',
        'review_date' => 'date',
        'male_owner_agree' => 'boolean',
        'Req_final_mark' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function managerUser(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'manager_user_id', 'id');
    }

    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'related_to_user_id', 'id');
    }

    public function breeding(): BelongsTo
    {
        return $this->belongsTo(PrevBreeding::class, 'related_breeding_process_id', 'id');
    }
}
