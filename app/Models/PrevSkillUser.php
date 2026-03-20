<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevSkillUser extends Pivot
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'users_skills';

    public $incrementing = true;

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'skill_id' => 'integer',
        'club_id' => 'integer',
        'breed_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(PrevSkill::class, 'skill_id', 'id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'club_id', 'id');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'breed_id', 'id');
    }
}
