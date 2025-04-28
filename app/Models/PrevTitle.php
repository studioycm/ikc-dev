<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;


class PrevTitle extends Model
{
    use SoftDeletes;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_prev';

    protected $table = 'dogs_titles_db';

    protected $fillable = [
        
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // append the title name to the model
    protected $appends = ['name'];

    // casting the attributes to the correct types
    protected $casts = [
        'TitleCode' => 'integer',
    ];
    // get name using TitleName attribute
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $this->TitleName,
        );
    }

    
}
