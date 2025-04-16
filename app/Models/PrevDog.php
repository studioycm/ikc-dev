<?php

namespace App\Models;

use App\Models\PrevHair;
use App\Models\PrevUser;
use App\Models\PrevBreed;
use App\Models\PrevColor;
use App\Models\PrevUserDog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // protected $fillable = [
    //     'SagirID',
    //     'Heb_Name',
    //     'Eng_Name',
    // ];

    // casting the attributes to the correct types
    protected $casts = [
        'SagirID' => 'integer',
        'FatherSAGIR' => 'integer',
        'MotherSAGIR' => 'integer',
        'Heb_Name' => 'string',
        'Eng_Name' => 'string',
        'GenderID' => 'integer',
        // 'ColorID' => 'integer',
        // 'HairID' => 'integer',
        // 'RaceID' => 'integer',
    ];

    // create maping for sagir_prefix (1"ISR", 2"IMP", 3"APX", 4"EXT", 5"NUL")
    const SAGIR_PREFIX_MAP = [
        1 => 'ISR',
        2 => 'IMP',
        3 => 'APX',
        4 => 'EXT',
        5 => 'NUL',
    ];

    // create maping for GenderID and Sex fields: 1="M", 2="F","ז"="M","נ"="F",null or any other = "n/a"
    const GenderMap = [
        1 => 'M',
        2 => 'F',
        'ז' => 'm',
        'נ' => 'f',
    ];

    // create an accessor for the sagir_prefix attribute using Laravel 12 syntax
    public function sagirPrefix(): Attribute
    {
        return new Attribute(
            get: fn($value) =>
                self::SAGIR_PREFIX_MAP[$value] ?? 'NUL'
        );
    }
    
    // create an accessor for the Gender attribute using Laravel 12 syntax
    public function gender(): Attribute
    {
        return new Attribute(
            get: fn() => isset($this->attributes['GenderID']) && array_key_exists((int)$this->attributes['GenderID'], self::GenderMap)
                        ? self::GenderMap[(int)$this->attributes['GenderID']]
                        : 'n/a'
        );
    }

    // eloquent relationships with PrevBreed and PrevColor
    public function breed(): HasOne 
    {
        return $this->hasOne(PrevBreed::class, 'BreedCode', 'RaceID');
    }
    public function color(): HasOne
    {
        return $this->hasOne(PrevColor::class, 'OldCode', 'ColorID');
    }
    
    public function hair(): HasOne
    {
        return $this->hasOne(PrevHair::class, 'OldCode', 'HairID');
    }
    // eloquent relationships with PrevDog
    
    public function father(): HasOne
    {
        return $this->hasOne(self::class, 'SagirID', 'FatherSAGIR');
    }

    public function mother(): HasOne
    {
        return $this->hasOne(self::class, 'SagirID', 'MotherSAGIR');
    }

    // users that are dog owners using dogs2users table or PrevUserDog model
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(PrevUser::class, 'dogs2users', 'SagirID', 'user_id', 'SagirID', 'id')
            ->withTimestamps()
            ->wherePivot('status', 'current')
            ->wherePivot('deleted_at', null);
    }

    // breedingManager using PrevUser model
    public function breedingManager(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'BreedingManagerID', 'id');
    }
    // currentOwner using PrevUser model
    public function currentOwner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'CurrentOwnerID', 'id');
    }

    // public function duplicates(): HasMany
    // {
    //     return $this->hasMany(self::class, 'SagirID', 'SagirID')
    //     ->withTrashed();
    // }

    protected function fullName(): Attribute
    {
        return new Attribute(
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
}
