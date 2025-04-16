<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrevUserDog extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dogs2users';

    // relationship with the dogs
}
