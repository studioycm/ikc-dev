<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\PrevTitle;

class PrevDogTitle extends Pivot
{
    use SoftDeletes;

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
    protected $table = 'Dogs_ScoresDB';

    protected $fillable = [
        'SagirID',
        'AwardID',
        'ShowID',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // append the title name to the model
    protected $appends = ['name'];

    // casting the attributes to the correct types
    protected $casts = [
        'SagirID' => 'integer',
        'AwardID' => 'integer',
        'ShowID' => 'integer',
        'EventDate' => 'date',
    ];

    public function title(): BelongsTo
    {
        return $this->belongsTo(PrevTitle::class, 'AwardID', 'TitleCode');
    }

    // get title name using AwardID
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => PrevTitle::find($attributes['AwardID'])->name,
        );
    }

    
}
