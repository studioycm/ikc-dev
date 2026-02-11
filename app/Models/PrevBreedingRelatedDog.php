<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevBreedingRelatedDog extends Model
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
    protected $table = 'breeding_related_dog';


    public $timestamps = true;
    protected $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'temparory_name',
        'chip_number',
        'sagir_id',
        'color',
        'other_color',
        'gender',
        'approval_status',
        'is_dead',
        'mother_sagir_id',
        'breeding_id',
        'note',
        'supervisor_notes',
        'image',
        'document',
        'updated_by',
        'hair',
        'is_submit',
    ];

    public function breeding(): BelongsTo
    {
        return $this->belongsTo(PrevBreeding::class, 'breeding_id', 'id');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'sagir_id', 'SagirID');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'mother_sagir_id', 'SagirID');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'updated_by', 'id');
    }

    public function colorName(): BelongsTo
    {
        return $this->belongsTo(PrevColor::class, 'color', 'id');
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'hair' => 'integer',
            'is_submit' => 'boolean',
        ];
    }
}
