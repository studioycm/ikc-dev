<?php

namespace App\Models;

use App\Casts\DecimalBooleanCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrevShowResult extends Model
{
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shows_results';
    protected $primaryKey = 'DataID';

    // disable fillable attributes
    protected $guarded = [];


    public $incrementing = true;

    protected $casts = [
        'RegDogID' => 'integer',
        'SagirID' => 'integer',
        'ShowOrderID' => 'integer',
        'MainArenaID' => 'integer',
        'SubArenaID' => 'integer',
        'ClassID' => 'integer',
        'ShowID' => 'integer',
        'GenderID' => 'integer',
        'BreedID' => 'integer',
        'Rank' => 'integer',
        'ModificationDateTime' => 'datetime',
        'CreationDateTime' => 'datetime',
        'JCAC' => DecimalBooleanCast::class,
        'GCAC' => DecimalBooleanCast::class,
        'REJCAC' => DecimalBooleanCast::class,
        'REGCAC' => DecimalBooleanCast::class,
        'CW' => DecimalBooleanCast::class,
        'BJ' => DecimalBooleanCast::class,
        'BV' => DecimalBooleanCast::class,
        'CAC' => DecimalBooleanCast::class,
        'RECACIB' => DecimalBooleanCast::class,
        'RECAC' => DecimalBooleanCast::class,
        'BP' => DecimalBooleanCast::class,
        'BB' => DecimalBooleanCast::class,
        'BOB' => DecimalBooleanCast::class,
        'Excellent' => DecimalBooleanCast::class,
        'Cannotbejudged' => DecimalBooleanCast::class,
        'VeryGood' => DecimalBooleanCast::class,
        'VeryPromising' => DecimalBooleanCast::class,
        'Good' => DecimalBooleanCast::class,
        'Promising' => DecimalBooleanCast::class,
        'Sufficient' => DecimalBooleanCast::class,
        'Satisfactory' => DecimalBooleanCast::class,
        'Disqualified' => DecimalBooleanCast::class,
        'CACIB' => DecimalBooleanCast::class,
        'BD' => DecimalBooleanCast::class,
        'BOS' => DecimalBooleanCast::class,
        'BPIS' => DecimalBooleanCast::class,
        'BPIS2' => DecimalBooleanCast::class,
        'BPIS3' => DecimalBooleanCast::class,
        'BJIS' => DecimalBooleanCast::class,
        'BJIS2' => DecimalBooleanCast::class,
        'BJIS3' => DecimalBooleanCast::class,
        'BVIS' => DecimalBooleanCast::class,
        'BVIS2' => DecimalBooleanCast::class,
        'BVIS3' => DecimalBooleanCast::class,
        'BIG' => DecimalBooleanCast::class,
        'BIG2' => DecimalBooleanCast::class,
        'BIG3' => DecimalBooleanCast::class,
        'BIS' => DecimalBooleanCast::class,
        'BIS2' => DecimalBooleanCast::class,
        'BIS3' => DecimalBooleanCast::class,
        'NotPresent' => DecimalBooleanCast::class,
        'NoTitle' => DecimalBooleanCast::class,
        'VCAC' => DecimalBooleanCast::class,
        'RVCAC' => DecimalBooleanCast::class,
        'BBaby' => DecimalBooleanCast::class,
        'BBIS' => DecimalBooleanCast::class,
        'BBIS2' => DecimalBooleanCast::class,
        'BBIS3' => DecimalBooleanCast::class,
        'BBaby2' => DecimalBooleanCast::class,
        'BBaby3' => DecimalBooleanCast::class,
        'VCACIB' => DecimalBooleanCast::class,
        'JCACIB' => DecimalBooleanCast::class,
    ];

    public function show(): BelongsTo
    {
        return $this->belongsTo(PrevShow::class, 'ShowID', 'id');
    }

    public function arena(): BelongsTo
    {
        return $this->belongsTo(PrevShowArena::class, 'MainArenaID', 'id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(PrevShowClass::class, 'ClassID', 'id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(PrevShowRegistration::class, 'RegDogID', 'DogId')
            ->where('ShowID', $this->ShowID);
    }

    public function showDog(): BelongsTo
    {
        return $this->belongsTo(PrevShowDog::class, 'SagirID', 'SagirID')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('shows_results as sr')
                    ->whereColumn('sr.SagirID', 'Shows_Dogs_DB.SagirID')
                    ->whereColumn('sr.ShowID', 'Shows_Dogs_DB.ShowID');
            });
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'BreedID', 'BreedCode');
    }
}
