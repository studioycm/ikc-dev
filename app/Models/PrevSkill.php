<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevSkill extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'skills';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'skill_access_level' => 'integer',
        'skill_status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function userSkills(): HasMany
    {
        return $this->hasMany(PrevSkillUser::class, 'skill_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'users_skills', 'skill_id', 'user_id')
            ->using(PrevSkillUser::class)
            ->withPivot('id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }
}
