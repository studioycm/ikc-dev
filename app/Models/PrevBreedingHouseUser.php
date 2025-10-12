<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PrevBreedingHouseUser extends Pivot
{
    protected $connection = 'mysql_prev';

    protected $table = 'breedhouses2users';

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'user_id' => 'integer',
        'breeding_house_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }

    public function breedingHouse(): BelongsTo
    {
        return $this->belongsTo(PrevBreedingHouse::class, 'breeding_house_id', 'id');
    }
}
