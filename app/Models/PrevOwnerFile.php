<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevOwnerFile extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'owner_files';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'owner_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'owner_id', 'id');
    }
}
