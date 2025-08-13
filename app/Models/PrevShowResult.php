<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowResult extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql_prev';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows_results';

    protected $primaryKey = 'DataID';
}
