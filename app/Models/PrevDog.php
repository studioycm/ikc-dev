<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrevDog extends Model
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
    protected $table = 'dogsdb';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'SagirID',
        'Heb_Name',
        'Eng_Name',
    ];
}
