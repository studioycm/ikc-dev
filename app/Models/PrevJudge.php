<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrevJudge extends Model
{
    protected $connection = 'mysql_prev';

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'JudgesDB';
}
