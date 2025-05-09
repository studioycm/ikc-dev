<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrevBreed extends Model
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

    // relationships with PrevUser model for UserManagerID and ClubManagerID
    public function userManager(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'UserManagerID', 'id');
    }
    public function clubManager(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'ClubManagerID', 'id');
    }
}
