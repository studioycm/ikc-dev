<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevBreedUser extends Pivot
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'user2breeds';

    public $incrementing = true;

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'breed_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'breed_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }
}
