<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrevColor extends Model
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
    protected $table = 'ColorsDB';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ColorNameHE',
        'ColorNameEN',
        'OldCode',
        'Remark',
    ];
}
