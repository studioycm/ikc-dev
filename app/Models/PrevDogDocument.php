<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevDogDocument extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'dogs_documents';

    protected $guarded = [];

    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'SagirID' => 'integer', // source is DECIMAL, we use int for relations
            'is_maag' => 'boolean',
            'result' => 'integer', // keep numeric, render as icon/boolean in UI later
            'TestDate' => 'datetime',
            'maag_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * The dog this document belongs to.
     */
    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID', 'SagirID');
    }
}
