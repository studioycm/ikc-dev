<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PrevClubManager extends Pivot
{
    protected $connection = 'mysql_prev';

    protected $table = 'user_club_manager';

    public $incrementing = true;

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'club_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'club_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }
}
