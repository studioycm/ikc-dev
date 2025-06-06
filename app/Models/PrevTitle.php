<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


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

    // revers many to many relationship with the PrevDog model with count using the pivot model PrevDogTitle
    public function dogs(): BelongsToMany
    {   
        // 'Dogs_ScoresDB', 'SagirID', 'AwardID', 'SagirID', 'TitleCode'
        return $this->belongsToMany(PrevDog::class, 'Dogs_ScoresDB', 'AwardID', 'SagirID', 'TitleCode', 'SagirID')
            ->using(PrevDogTitle::class)
            ->as('awarding')
            ->where('DogsDB.deleted_at', null);
    }

    // count the number of dogs with this title
    

    // get name using TitleName attribute
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $this->TitleName,
        );
    }

    
}
