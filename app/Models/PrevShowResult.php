<?php

namespace App\Models;

use App\Casts\DecimalBooleanCast;
use App\Casts\Legacy\LegacyDogGenderCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Title columns - awards and certificates given at shows
     * Structure: column_name => [label, color, icon]
     */
    public const array TITLE_COLUMNS = [
        // CAC titles
        'JCAC' => ['label' => 'JCAC', 'color' => 'warning', 'icon' => 'fas-trophy'],
        'REJCAC' => ['label' => 'Res. JCAC', 'color' => 'warning', 'icon' => 'fas-trophy'],
        'GCAC' => ['label' => 'GCAC', 'color' => 'gray', 'icon' => 'fas-trophy'],
        'REGCAC' => ['label' => 'Res. GCAC', 'color' => 'gray', 'icon' => 'fas-trophy'],
        'CAC' => ['label' => 'CAC', 'color' => 'success', 'icon' => 'fas-trophy'],
        'RECAC' => ['label' => 'Res. CAC', 'color' => 'success', 'icon' => 'fas-trophy'],
        'VCAC' => ['label' => 'VCAC', 'color' => 'purple', 'icon' => 'fas-trophy'],
        'RVCAC' => ['label' => 'Res. VCAC', 'color' => 'purple', 'icon' => 'fas-trophy'],

        // CACIB titles
        'JCACIB' => ['label' => 'JCACIB', 'color' => 'warning', 'icon' => 'fas-trophy'],
        'CACIB' => ['label' => 'CACIB', 'color' => 'success', 'icon' => 'fas-trophy'],
        'RECACIB' => ['label' => 'Res. CACIB', 'color' => 'success', 'icon' => 'fas-trophy'],
        'VCACIB' => ['label' => 'VCACIB', 'color' => 'purple', 'icon' => 'fas-trophy'],

        // Best titles
        'BBaby' => ['label' => 'Best Baby', 'color' => 'pink', 'icon' => 'fas-medal'],
        'BBaby2' => ['label' => 'Best Baby 2', 'color' => 'pink', 'icon' => 'fas-medal'],
        'BBaby3' => ['label' => 'Best Baby 3', 'color' => 'pink', 'icon' => 'fas-medal'],
        'BJ' => ['label' => 'Best Junior', 'color' => 'warning', 'icon' => 'fas-medal'],
        'BP' => ['label' => 'Best Puppy', 'color' => 'pink', 'icon' => 'fas-medal'],
        'BV' => ['label' => 'Best Veteran', 'color' => 'purple', 'icon' => 'fas-medal'],
        'BB' => ['label' => 'Best Bitch', 'color' => 'info', 'icon' => 'fas-medal'],
        'BD' => ['label' => 'Best Dog', 'color' => 'info', 'icon' => 'fas-medal'],
        'BOB' => ['label' => 'Best of Breed', 'color' => 'success', 'icon' => 'fas-medal'],
        'BOS' => ['label' => 'Best Opposite Sex', 'color' => 'success', 'icon' => 'fas-medal'],
        'CW' => ['label' => 'Class Winner', 'color' => 'info', 'icon' => 'fas-medal'],

        // BBIS titles
        'BBIS' => ['label' => 'Best Baby in Show', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BBIS2' => ['label' => 'BBIS 2nd', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BBIS3' => ['label' => 'BBIS 3rd', 'color' => 'danger', 'icon' => 'fas-medal'],

        // BPIS titles
        'BPIS' => ['label' => 'Best Puppy in Show', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BPIS2' => ['label' => 'BPIS 2nd', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BPIS3' => ['label' => 'BPIS 3rd', 'color' => 'danger', 'icon' => 'fas-medal'],

        // BJIS titles
        'BJIS' => ['label' => 'Best Junior in Show', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BJIS2' => ['label' => 'BJIS 2nd', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BJIS3' => ['label' => 'BJIS 3rd', 'color' => 'danger', 'icon' => 'fas-medal'],

        // BIS titles
        'BIS' => ['label' => 'Best in Show', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BIS2' => ['label' => 'BIS 2nd', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BIS3' => ['label' => 'BIS 3rd', 'color' => 'danger', 'icon' => 'fas-medal'],

        // BVIS titles
        'BVIS' => ['label' => 'Best Veteran in Show', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BVIS2' => ['label' => 'BVIS 2nd', 'color' => 'danger', 'icon' => 'fas-medal'],
        'BVIS3' => ['label' => 'BVIS 3rd', 'color' => 'danger', 'icon' => 'fas-medal'],

        // BIG titles
        'BIG' => ['label' => 'Best in Group', 'color' => 'warning', 'icon' => 'fas-medal'],
        'BIG2' => ['label' => 'BIG 2nd', 'color' => 'warning', 'icon' => 'fas-medal'],
        'BIG3' => ['label' => 'BIG 3rd', 'color' => 'warning', 'icon' => 'fas-medal'],
    ];

    /**
     * Result columns - judging results and ratings
     * Structure: column_name => [label, color, icon]
     */
    public const array RESULT_COLUMNS = [
        'Excellent' => ['label' => 'Excellent', 'color' => 'success', 'icon' => 'fas-award'],
        'VeryGood' => ['label' => 'Very Good', 'color' => 'info', 'icon' => 'fas-award'],
        'VeryPromising' => ['label' => 'Very Promising', 'color' => 'info', 'icon' => 'fas-award'],
        'Good' => ['label' => 'Good', 'color' => 'warning', 'icon' => 'fas-award'],
        'Promising' => ['label' => 'Promising', 'color' => 'warning', 'icon' => 'fas-award'],
        'Sufficient' => ['label' => 'Sufficient', 'color' => 'gray', 'icon' => 'fas-award'],
        'Satisfactory' => ['label' => 'Satisfactory', 'color' => 'gray', 'icon' => 'fas-award'],
        'Cannotbejudged' => ['label' => 'Cannot Be Judged', 'color' => 'gray', 'icon' => 'fas-award'],
        'Disqualified' => ['label' => 'Disqualified', 'color' => 'danger', 'icon' => 'fas-award'],
        'NotPresent' => ['label' => 'Not Present', 'color' => 'gray', 'icon' => 'fas-award'],
        'NoTitle' => ['label' => 'No Title', 'color' => 'gray', 'icon' => 'fas-award'],
    ];

    protected $casts = [
        'RegDogID' => 'integer',
        'SagirID' => 'integer',
        'ShowOrderID' => 'integer',
        'MainArenaID' => 'integer',
        'SubArenaID' => 'integer',
        'ClassID' => 'integer',
        'ShowID' => 'integer',
        'GenderID' => LegacyDogGenderCast::class,
        'BreedID' => 'integer',
        'Rank' => 'integer',
        'ModificationDateTime' => 'datetime',
        'CreationDateTime' => 'datetime',
        'Excellent' => DecimalBooleanCast::class,
        'Cannotbejudged' => DecimalBooleanCast::class,
        'VeryGood' => DecimalBooleanCast::class,
        'VeryPromising' => DecimalBooleanCast::class,
        'Good' => DecimalBooleanCast::class,
        'Promising' => DecimalBooleanCast::class,
        'Sufficient' => DecimalBooleanCast::class,
        'Satisfactory' => DecimalBooleanCast::class,
        'Disqualified' => DecimalBooleanCast::class,
        'NotPresent' => DecimalBooleanCast::class,
        'NoTitle' => DecimalBooleanCast::class,
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

    protected function dogName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->resultDog?->full_name
        );
    }

    protected function breedName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->breed?->BreedName ?? null
        );
    }

    protected function breedNameEn(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->breed?->BreedNameEN ?? null
        );
    }

    // --- Accessors (names) ---
    protected function titlesList(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->filterActiveNames(self::TITLE_COLUMNS)
        );
    }

    protected function resultsList(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->filterActiveNames(self::RESULT_COLUMNS)
        );
    }

    // --- Accessors (labels) ---
    protected function titlesLabels(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->filterActiveLabels(self::TITLE_COLUMNS)
        );
    }

    protected function resultsLabels(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->filterActiveLabels(self::RESULT_COLUMNS)
        );
    }

    // --- Accessors (metadata) ---
    protected function titlesWithMeta(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->filterActiveMeta(self::TITLE_COLUMNS)
        );
    }

    protected function resultsWithMeta(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->filterActiveMeta(self::RESULT_COLUMNS)
        );
    }

    // --- Helpers (pure, O(n) over defined constants) ---
    private function filterActiveNames(array $map): array
    {
        $out = [];
        foreach ($map as $col => $_) {
            if (isset($this->attributes[$col]) && $this->{$col} === true) {
                $out[] = $col;
            }
        }
        return $out;
    }

    private function filterActiveLabels(array $map): array
    {
        $out = [];
        foreach ($map as $col => $cfg) {
            if (isset($this->attributes[$col]) && $this->{$col} === true) {
                $out[] = $cfg['label'] ?? $col;
            }
        }
        return $out;
    }

    private function filterActiveMeta(array $map): array
    {
        $out = [];
        foreach ($map as $col => $cfg) {
            if (isset($this->attributes[$col]) && $this->{$col} === true) {
                $out[] = [
                    'column' => $col,
                    'label' => $cfg['label'] ?? $col,
                    'color' => $cfg['color'] ?? null,
                    'icon' => $cfg['icon'] ?? null,
                ];
            }
        }
        return $out;
    }

    // --- Static helpers (optional, for tests/UI composition) ---
    public static function getTitleColumnNames(): array
    {
        return array_keys(self::TITLE_COLUMNS);
    }

    public static function getResultColumnNames(): array
    {
        return array_keys(self::RESULT_COLUMNS);
    }

    public static function getColumnLabel(string $column): ?string
    {
        return self::TITLE_COLUMNS[$column]['label'] ?? self::RESULT_COLUMNS[$column]['label'] ?? null;
    }

    public static function getColumnColor(string $column): ?string
    {
        return self::TITLE_COLUMNS[$column]['color'] ?? self::RESULT_COLUMNS[$column]['color'] ?? null;
    }

    public static function getColumnIcon(string $column): ?string
    {
        return self::TITLE_COLUMNS[$column]['icon'] ?? self::RESULT_COLUMNS[$column]['icon'] ?? null;
    }

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
                    ->whereColumn('sr.ShowID', 'Shows_Dogs_DB.ShowID')
                    ->whereColumn('sr.MainArenaID', 'Shows_Dogs_DB.ArenaID')
                    ->whereColumn('sr.ClassID', 'Shows_Dogs_DB.ClassID');
            });
    }

    public function resultDog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'SagirID', 'SagirID')
            ->select(['SagirID', 'Heb_Name', 'Eng_Name', 'RaceID', 'GenderID', 'BirthDate']);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(PrevBreed::class, 'BreedID', 'BreedCode')
            ->select(['BreedCode', 'BreedName', 'BreedNameEN']);
    }


}
