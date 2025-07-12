<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShow extends Model
{
    use SoftDeletes;

    protected $table = 'ShowsDB';

    protected $fillable = ['TitleName', 'created_at', 'updated_at', 'deleted_at'];

    protected $appends = ['name'];

    public function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->attributes['TitleName'];
            }
        );
    }

}
