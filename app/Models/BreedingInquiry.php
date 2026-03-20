<?php

namespace App\Models;

use App\Enums\BreedingInquiryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreedingInquiry extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => BreedingInquiryStatus::class,
            'breeding_date' => 'date',
            'breeding_rights' => 'array',
            'birthing_date' => 'date',
            'puppies' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function femaleDog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'female_sagir_id', 'SagirID');
    }

    public function maleDog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'male_sagir_id', 'SagirID');
    }

    public function breedingHouse(): BelongsTo
    {
        return $this->belongsTo(PrevBreedingHouse::class, 'prev_breeding_house_id', 'id');
    }

    public function breeder(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'prev_breeder_id', 'id');
    }
}
