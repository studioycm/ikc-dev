<?php

namespace App\Models;

use App\Casts\Legacy\LegacyDogGenderCast;
use App\Casts\Legacy\LegacyDogSizeCast;
use App\Casts\Legacy\LegacyDogStatusCast;
use App\Enums\Legacy\LegacyPedigreeColor;
use App\Enums\Legacy\LegacySagirPrefix;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevDog extends Model
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


    protected $table = 'DogsDB';

    public $timestamps = true;

    // Disable Fillable Attributes
    protected $guarded = [];

    // casting the attributes to the correct types
    protected $casts = [
        'SagirID' => 'integer',
        'FatherSAGIR' => 'integer',
        'MotherSAGIR' => 'integer',
        'Heb_Name' => 'string',
        'Eng_Name' => 'string',
        'ColorID' => 'integer',
        'HairID' => 'integer',
        'RaceID' => 'integer',
        'BeitGidulID' => 'integer',
        'CurrentOwnerId' => 'integer',
        'GrowerId' => 'integer',
        'GroupID' => 'integer',
        'ShowsCount' => 'integer',
        'IsMagPass' => 'integer',
        'IsMagPass_2' => 'integer',
        'SCH' => 'integer',
        'BreedID' => 'integer',
        'GenderID' => LegacyDogGenderCast::class,
        'SizeID' => LegacyDogSizeCast::class,
        'Status' => LegacyDogStatusCast::class,
        'pedigree_color' => LegacyPedigreeColor::class,
        'sagir_prefix' => LegacySagirPrefix::class,
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    //    public function getRouteKeyName(): string
    //    {
    //        return 'SagirID';
    //    }

    // create mapping for GenderID and Sex fields: 1="M", 2="F","ז"="M","נ"="F",null or any other = "n/a"
    const array GenderMap = [
        1 => 'M',
        2 => 'F',
        'ז' => 'm',
        'נ' => 'f',
    ];

    // eloquent relationships with PrevBreed and PrevColor
    public function breed(): BelongsTo
    {
        // Dog belongs to a breed: RaceID (dogs) -> BreedCode (breeds)
        return $this->belongsTo(PrevBreed::class, 'RaceID', 'BreedCode');
    }

    public function color(): BelongsTo
    {
        // Dog belongs to a color: ColorID (dogs) -> OldCode (colors)
        return $this->belongsTo(PrevColor::class, 'ColorID', 'OldCode');
    }

    public function hair(): BelongsTo
    {
        // Dog belongs to a hair type: HairID (dogs) -> OldCode (hairs)
        return $this->belongsTo(PrevHair::class, 'HairID', 'OldCode');
    }
    // eloquent relationships with self PrevDog model as a father and mother

    public function father(): BelongsTo
    {
        return $this->belongsTo(self::class, 'FatherSAGIR', 'SagirID');
    }

    public function mother(): BelongsTo
    {
        return $this->belongsTo(self::class, 'MotherSAGIR', 'SagirID');
    }

    public function childrenAsFather(): HasMany
    {
        // All pups that list this dog as FatherSAGIR
        return $this->hasMany(self::class, 'FatherSAGIR', 'SagirID');
    }

    public function childrenAsMother(): HasMany
    {
        // All pups that list this dog as MotherSAGIR
        return $this->hasMany(self::class, 'MotherSAGIR', 'SagirID');
    }

    public function breedinghouse(): BelongsTo
    {
        return $this->belongsTo(PrevBreedingHouse::class, 'BeitGidulID', 'GidulCode');
    }

    // users that are dog owners using dogs2users table or PrevUserDog model
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'dogs2users', 'sagir_id', 'user_id', 'SagirID', 'id')
            ->withTimestamps()
            ->using(PrevUserDog::class)
            ->as('ownership')
            ->withPivot('status', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivot('deleted_at', null)
            ->wherePivot('status', 'current');
    }

    // get dog titles by a relationship of many 2 many with PrevDogTitle model
    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(PrevTitle::class, 'Dogs_ScoresDB', 'SagirID', 'AwardID', 'SagirID', 'TitleCode')
            ->where('Dogs_ScoresDB.deleted_at', null)
            ->withTimestamps()
            ->using(PrevDogTitle::class)
            ->as('awarding')
            ->withPivot('AwardID', 'EventPlace', 'EventName', 'EventDate', 'ShowID', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivot('deleted_at', null)
            ->orderBy('EventDate', 'desc');
    }

    // breedingManager using PrevUser model
    public function breedingManager(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'Breeding_ManagerID', 'id');
    }

    // current_owner dog owner registered pre 2022 using belongs-to relation with foreign key.
    // post 2022 we use belongs-to-many relation "owners" with pivot model PrevUserDog
    public function currentOwner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'CurrentOwnerId', 'owner_code');
    }

    /**
     * All documents linked to this dog (by SagirID).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PrevDogDocument::class, 'SagirID', 'SagirID');
    }

    /**
     * All health records linked to this dog (by SagirID).
     */
    public function healthRecords(): HasMany
    {
        return $this->hasMany(PrevHealth::class, 'SagirID', 'SagirID');
    }

    public function duplicates(): HasMany
    {
        $relation = $this->hasMany(self::class, 'SagirID', 'SagirID');

        $relation->withTrashed();

        return $relation;
    }

    // appends full_name and prefixed_sagir, removed the "sagir_prefix" and "gender" attributes
    protected $appends = ['full_name', 'breeding_house_name'];

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $heb = (($v = trim((string)($this->Heb_Name ?? ''))) !== '') ? $v : null;
                $eng = (($v = trim((string)($this->Eng_Name ?? ''))) !== '') ? $v : null;

                if ($heb === null && $eng === null) {
                    return '---';
                }
                if ($heb === null) {
                    return $eng;
                }
                if ($eng === null) {
                    return $heb;
                }
                return "{$heb} | {$eng}";
            }
        );
    }

    protected function breedingHouseName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->breedinghouse?->name ?? '---'
        );
    }

    // simple accessor to get a human-friendly label anywhere.
    public function genderLabel(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->GenderID->getLabel()
        );
    }

    public function sizeLabel(): Attribute
    {
        return Attribute::make(
            get: fn(): string => $this->SizeID->getLabel()
        );
    }

    /**
     * All show entries for this dog.
     */
    public function showDogs(): HasMany
    {
        return $this->hasMany(PrevShowDog::class, 'SagirID', 'SagirID');
    }


}
