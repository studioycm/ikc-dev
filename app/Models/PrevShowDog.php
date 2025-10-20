<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShowDog extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Dogs_DB';

    // disable fillable attributes
    protected $guarded = [];

    protected $casts = [
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'ArenaID' => 'integer',
        'ClassID' => 'integer',
        'ShowRegistrationID' => 'integer',
        'new_show_registration_id' => 'integer',
        'OwnerID' => 'integer',
        'BreedID' => 'integer',
        'SagirID' => 'integer',
    ];

    protected $appends = ['heb_dog_name', 'eng_dog_name'];

    // related dog's hebrew name quick accessor with laravel new attribute style
    protected function hebDogName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->dog?->Heb_Name ?? '-',
        );
    }

    protected function engDogName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->dog?->Eng_Name ?? '-',
        );
    }

    protected function dogName(): Attribute
    {
        // return hebrew name if available, else english name
        return Attribute::make(
            get: fn($value) => $this->dog?->Heb_Name ?? $this->dog?->Eng_Name ?? '-',
        );
    }

    // Normalized relation names
    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id');
    }

    public function arena(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'ArenaID', 'id');
    }

    public function showClass(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID', 'DataID');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'ShowRegistrationID', 'id');
    }

    public function newRegistration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'new_show_registration_id', 'id');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID', 'SagirID');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'BreedID', 'BreedCode');
    }

    // revers relation with PrevShowResult
    public function prevShowResult(): HasOne
    {
        // Bind by literals so eager loading does not inject nulls or try to reference parent table
        return $this->hasOne(PrevShowResult::class, 'SagirID', 'SagirID')
            ->where('shows_results.ShowID', $this->ShowID)
            // If arena must match too, keep this line; otherwise remove it:
            ->when($this->ArenaID !== null, fn($q) => $q->where('shows_results.MainArenaID', $this->ArenaID))
            ->orderBy('shows_results.DataID', 'asc');
    }
}
