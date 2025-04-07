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
    protected $fillable = [
        'SagirID',
        'Heb_Name',
        'Eng_Name',
    ];

    // create an enum for sagir_prefix (values: 1, 2, 3, 4, 5) and maping them to ("ISR", "IMP", "APX", "EXT", "NUL")
    const SAGIR_PREFIX = [
        1 => 'ISR',
        2 => 'IMP',
        3 => 'APX',
        4 => 'EXT',
        5 => 'NUL',
    ];

    // how do we use the SAGIR_PREFIX to cast the sagir_prefix to the correct value?
    // create a mutator for the sagir_prefix attribute
    public function getSagirPrefixAttribute()
    {
        return self::SAGIR_PREFIX[$this->attributes['sagir_prefix']] ?? 'NUL';
    }

    // casting the attributes to the correct types
    protected $casts = [
        'SagirID' => 'integer',
        'FatherSAGIR' => 'integer',
        'MotherSAGIR' => 'integer',
        'Heb_Name' => 'string',
        'Eng_Name' => 'string',
    ];
    
    
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
        return $this->belongsTo(self::class, 'FatherSAGIR', 'SagirID');
    }

    public function mother()
    {
        return $this->belongsTo(self::class, 'MotherSAGIR', 'SagirID');
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
