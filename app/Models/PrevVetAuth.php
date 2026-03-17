<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrevVetAuth extends Model
{
    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agra_cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'vet_email',
    ];

    public function userRequest(): HasMany
    {
        return $this->hasMany(PrevUserRequest::class, 'agra_city', 'id');
    }
}
