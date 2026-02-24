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
            'puppies' => 'array',
            'status' => BreedingInquiryStatus::class,
            'breeding_date' => 'date',
            'birthing_date' => 'date',
            'submitted_at' => 'datetime',
            'breeding_rights' => 'array',
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
}
