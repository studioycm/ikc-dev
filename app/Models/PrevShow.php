<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShow extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ShowsDB';

    protected $casts = [
        'ShowID' => 'integer',
    ];

    public function showClasses()
    {
        return $this->hasMany(PrevShowClass::class, 'ShowID');
    }

    public function showArenas()
    {
        return $this->hasMany(PrevShowArena::class, 'ShowID');
    }

    public function showArenasWithClasses()
    {
        return $this->hasMany(PrevShowArena::class, 'ShowID')->with('showClasses');
    }




}
