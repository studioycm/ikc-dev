<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevDogImport extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'DogsInfo';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'dog_import_sagir' => 'integer',
        'dog_birth_date' => 'date',
        'dog_country_id' => 'integer',
        'user_id' => 'integer',
        'dog_sagir_id' => 'integer',
        'dog_country_id_2' => 'integer',
        'dog_country_id_3' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }
}
