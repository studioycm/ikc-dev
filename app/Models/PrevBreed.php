<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrevBreed extends Model
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
    protected $table = 'BreedsDB';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'BreedName',
        'BreedNameEN',
        'BreedCode',
        'FCICODE',
        'fci_group',
        'GroupID',
    ];
}
