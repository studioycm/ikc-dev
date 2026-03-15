<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevPrice extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'pricing';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'price' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
