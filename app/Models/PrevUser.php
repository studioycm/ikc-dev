<?php

namespace App\Models;

use App\Models\PrevDog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;


class PrevUser extends Model
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
    protected $table = 'users';

    protected $primaryKey = 'id';

    // relationship with the dogs

    public function dogs()
    {
        return $this->hasMany(PrevDog::class, 'dogs2users', 'user_id', 'id');
    }

    // get hebrew full name and english full name - from first and last name, add checks which exist and then try to have both 
    public function getFullNameHEAttribute()
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';

        return trim($firstName . ' ' . $lastName);
    }
    public function getFullNameENAttribute()
    {
        $firstName = $this->first_name_en ?? '';
        $lastName = $this->last_name_en ?? '';

        return trim($firstName . ' ' . $lastName);
    }
    
    public function fullName(): Attribute
    {
        return new Attribute(
            get: fn () => ($this->first_name && $this->last_name)
                    ? $this->first_name . ' ' . $this->last_name
                    : (($this->first_name_en && $this->last_name_en)
                        ? $this->first_name_en . ' ' . $this->last_name_en
                        : '<< Name Not Found >>')
            );
    }
    
    
   
    

}
