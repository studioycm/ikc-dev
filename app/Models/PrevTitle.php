<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class PrevTitle extends Model
{
    protected $primaryKey = 'TitleCode';
    public $incrementing = false;
    protected $keyType = 'int';
    use SoftDeletes;

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'mysql_prev';

    protected $table = 'dogs_titles_db';

    protected $fillable = [
        'TitleCode',
        'TitleName',
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

    // relationship of many scores (titles) given by Dogs_ScoresDB.AwardID and dogs_titles_db.TitleCode

    public function awarding(): HasMany
    {
        return $this->hasMany(PrevDogTitle::class, 'AwardID', 'TitleCode');
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
