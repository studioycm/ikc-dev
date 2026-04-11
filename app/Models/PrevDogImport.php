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
        'dog_birth_date' => 'date',
        'dog_country_id' => 'integer',
        'user_id' => 'integer',
        'dog_sagir_id' => 'integer',
        'dog_country_id_2' => 'integer',
        'dog_country_id_3' => 'integer',
        'dog_breed' => 'integer',
        'dog_hair_color' => 'integer',
        'dog_chip' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'dog_sagir_id', 'SagirID');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'dog_breed', 'BreedCode');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(PrevColor::class, 'dog_hair_color', 'OldCode');
    }
}
