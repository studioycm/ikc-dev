<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrevShowBreed extends Model
{
    protected $connection = 'mysql_prev';

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Shows_Breeds';

    protected $primaryKey = 'DataID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];

    protected $casts = [
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'ArenaID' => 'integer',
        'MainArenaID' => 'integer',
        'RaceID' => 'integer',
        'JudgeID' => 'integer',
        'OrderID' => 'integer',
        'count' => 'integer',
    ];

    // append the breed name to the model
    protected $appends = ['judge_he_name', 'breed_he_name'];

    // new format laravel attributes for judgeHeName, judgeEnName, breedHeName, breedEnName
    protected function judgeHeName(): Attribute
    {
        // efficient way to get the name of the judge name only
        return Attribute::make(
            get: fn($value) => $this->judge?->JudgeNameHE ?? '-',
        );
    }

    protected function judgeEnName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->judge?->JudgeNameEN ?? '-',
        );
    }

    protected function breedHeName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->breed?->BreedName ?? '-',
        );
    }

    protected function breedEnName(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->breed?->BreedNameEN ?? '-',
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

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'RaceID', 'BreedCode');
    }

    public function judge(): BelongsTo
    {
        return $this->belongsTo(PrevJudge::class, 'JudgeID', 'DataID');
    }

    public function showDogs(): HasMany
    {
        return $this->hasMany(PrevShowDog::class, 'BreedID', 'DataID');
    }
}
