<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrevDog extends Model
{
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

    // create a mutator for the sagir_prefix attribute
    public function getPrefixAttribute()
    {
        return self::SAGIR_PREFIX_MAP[$this->attributes['sagir_prefix']] ?? 'NUL';
    }
    
    // create a mutator for the GenderID attribute
    public function getGenderAttribute()
    {
        $genderIDKey = isset($this->attributes['GenderID']) ? (int)$this->attributes['GenderID'] : null;
        return array_key_exists($genderIDKey, self::GenderMap) ? self::GenderMap[$genderIDKey] : 'n/a';
    }

    // create a mutator for the GenderID and Sex attribute
    public function getGenderSexAttribute()
    {
                $sexKey = isset($this->attributes['Sex']) ? trim((string)$this->attributes['Sex']) : null;
                $genderIDKey = isset($this->attributes['GenderID']) ? (int)$this->attributes['GenderID'] : null;

                $mappedSex = array_key_exists($sexKey, self::GenderMap) ? self::GenderMap[$sexKey] : null;
                $mappedGenderID = array_key_exists($genderIDKey, self::GenderMap) ? self::GenderMap[$genderIDKey] : null;

                if (!empty($mappedSex) && !empty($mappedGenderID)) {
                    return $mappedGenderID;
                } elseif (!empty($mappedSex)) {
                    return $mappedSex;
                } elseif (!empty($mappedGenderID)) {
                    return $mappedGenderID;
                }
                return 'n/a';
    }
    // eloquent relationships with PrevBreed and PrevColor
    public function breed()
    {
        return $this->hasOne(PrevBreed::class, 'BreedCode', 'RaceID');
    }
    public function color()
    {
        return $this->hasOne(PrevColor::class, 'OldCode', 'ColorID');
    }
    public function hair()
    {
        return $this->hasOne(PrevHair::class, 'OldCode', 'HairID');
    }
    // eloquent relationships with PrevDog
    
    public function father()
    {
        return $this->hasOne(self::class, 'SagirID', 'FatherSAGIR');
    }

    public function mother()
    {
        return $this->hasOne(self::class, 'SagirID', 'MotherSAGIR');
    }
   /* 
    // a dog has one father using the dog's field FatherSAGIR as the foreign key and SagirID as the local key, the father has many dogs
    public function father()
    {
        return $this->hasOne(PrevDog::class, 'SagirID', 'FatherSAGIR');
    }
    // a dog has one mother using the dog's field MotherSAGIR as the foreign key and SagirID as the local key, the mother has many dogs
    public function mother()
    {
        return $this->hasOne(PrevDog::class, 'SagirID', 'MotherSAGIR');
    }
    */
}
