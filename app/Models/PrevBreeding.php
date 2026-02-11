<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevBreeding extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';
    protected $table = 'breedings';
    public $timestamps = true;
    protected $guarded = [];
    protected $primaryKey = 'id';

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'BreddingDate' => 'date',
            'birthing_date' => 'date',
            'SagirId' => 'integer',
            'MaleSagirId' => 'integer',
            'total_refund' => 'integer',
            'total_payment' => 'integer',
            'certificate_price' => 'integer',
            'review_price' => 'integer',
            'price_per_dog' => 'integer',
            'breeding_house_id' => 'integer',
            'Rules_IsOwner' => 'boolean',
            'BreedMismatch' => 'boolean',
            'Male_More_Than_5' => 'boolean',
            'Male_More_Than_2' => 'boolean',
            'Male_DNA' => 'boolean',
            'Male_Breeding_Not_Approved' => 'boolean',
            'Female_Breeding_Not_Approved' => 'boolean',
            'Female_DNA' => 'boolean',
            'Foreign_Male_Records' => 'boolean',
            'publish_data' => 'boolean',
            'share_data' => 'boolean',
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'SagirId',
        'BreddingDate',
        'MaleSagirId',
        'Rules_IsOwner',
        'BreedMismatch',
        'Male_More_Than_5',
        'Male_More_Than_2',
        'Male_DNA',
        'Female_DNA',
        'Male_Breeding_Not_Approved',
        'Female_Breeding_Not_Approved',
        'Foreign_Male_Records',
        'female_rate',
        'male_rebreed',
        'male_rebreed_5',
        'male_rebreed_2',
        'generations_note',
        'live_male_puppie',
        'live_female_puppie',
        'dead_male_puppie',
        'dead_female_puppie',
        'total_dead',
        'review_type',
        'publish_data',
        'share_data',
        'birthing_date',
        'filled_step',
        'payment_type',
        'payment_status',
        'price_per_dog',
        'review_price',
        'certificate_price',
        'total_payment',
        'total_refund',
        'less_than_8_years',
        'more_than_18_months',
        'status',
        'responsiable_owner',
        'created_by',
        'breeding_house_id',
        'Breeding_ManagerID',
    ];

    public function breedinghouse(): BelongsTo
    {
        return $this->belongsTo(PrevBreedingHouse::class, 'breeding_house_id', 'id');
    }

    public function female(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirId', 'SagirID');
    }

    public function male(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'MaleSagirId', 'SagirID');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'created_by', 'id');
    }

    public function puppies(): HasMany
    {
        return $this->hasMany(PrevBreedingRelatedDog::class, 'breeding_id', 'id');
    }

}
