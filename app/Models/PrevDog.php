<?php

namespace App\Models;

use App\Casts\Legacy\LegacyDogGenderCast;
use App\Casts\Legacy\LegacyDogSizeCast;
use App\Enums\Legacy\LegacyDogStatus;
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
        'GenderID' => LegacyDogGenderCast::class,
        'SizeID' => LegacyDogSizeCast::class,
        'Status' => LegacyDogStatus::class,
        'pedigree_color' => LegacyPedigreeColor::class,
        'sagir_prefix' => LegacySagirPrefix::class,
    ];

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
        return $this->belongsTo(PrevUser::class, 'BreedingManagerID', 'id');
    }

    // current_owner using "DogsOwners" table from connection "mysql_prev" without a dedicated model
    public function currentOwner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'CurrentOwnerId', 'owner_code');
    }

    public function duplicates(): HasMany
    {
        $relation = $this->hasMany(self::class, 'SagirID', 'SagirID');

        $relation->withTrashed();

        return $relation;
    }

    // appends full_name and prefixed_sagir, removed the "sagir_prefix" and "gender" attributes
    protected $appends = ['full_name'];

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $heb = $this->attributes['Heb_Name'] ?? null;
                $eng = $this->attributes['Eng_Name'] ?? null;

                if ($heb && $eng) {
                    return $heb . ' | ' . $eng;
                }
                if ($heb) {
                    return $heb;
                }
                if ($eng) {
                    return $eng;
                }

                return '<< Name Not Found >>';
            }
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
}
