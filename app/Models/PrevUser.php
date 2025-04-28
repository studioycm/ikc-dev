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

    public function dogs(): BelongsToMany
    {
        return $this->belongsToMany(PrevDog::class, 'dogs2users', 'user_id', 'SagirID', 'id', 'SagirID')
        ->withTimestamps()
        ->using(PrevUserDog::class)
        ->as('ownership')
        ->withPivot('status', 'created_at', 'updated_at', 'deleted_at')
        ->wherePivot('deleted_at', null)
        ->wherePivot('status', 'current');
    }

    public function history_dogs(): HasMany
    {
        return $this->hasMany(PrevDog::class, 'CurrentOwnerId', 'owner_code')
        ->where('deleted_at', null);
    }


    protected $appends = ['full_name', 'name'];
    
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

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn() => tap(
                // try Hebrew first
                trim(implode(' ', array_filter([$this->first_name, $this->last_name]))),
                fn(&$n) => $n || $n = trim(implode(' ', array_filter([$this->first_name_en, $this->last_name_en])))
            ) ?: '---'
        );
    }

}
