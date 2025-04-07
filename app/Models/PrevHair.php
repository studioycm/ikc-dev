<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrevHair extends Model
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
    protected $table = 'HairsDB';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'HairNameHE',
        'HairNameEN',
        'OldCode',
        'Remark',
    ];
}
