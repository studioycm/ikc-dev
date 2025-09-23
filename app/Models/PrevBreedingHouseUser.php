<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PrevBreedingHouseUser extends Pivot
{
    protected $connection = 'mysql_prev';

    protected $table = 'breedhouses2users';

    public $timestamps = true;

    protected $guarded = [];
}
