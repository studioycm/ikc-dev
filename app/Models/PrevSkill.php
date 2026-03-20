<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevSkill extends Model
{
    use SoftDeletes;

    public const array GROUPS = [
        // Senior leadership of the Association (IKC) and high-level managers
        'management' => [14, 15, 16, 17],

        // Specific committees (Breeding, Audit, Midrasha, Exhibitions)
        'committees' => [4, 13, 18, 19, 20, 21, 22, 23, 24, 25, 26, 35],

        // Club-level specific roles (Chair, Secretary, Treasurer, Show manager)
        'club' => [3, 5, 7, 8, 10, 11],

        // Daily operations, data entry, and central office staff
        'office' => [6, 12, 27, 28, 30, 32, 34, 36],

        // Breed-specific roles and field activities
        'general' => [1, 9, 29, 31, 33],

        // Placeholders or unclassified items
        'other' => [2],
    ];

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
            ->withPivot('id', 'club_id', 'breed_id', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivotNull('deleted_at');
    }
}
