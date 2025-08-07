<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PrevUserDog extends Pivot
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
    protected $table = 'dogs2users';

    // relationship with the dogs
    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'sagir_id', 'SagirID');
    }
    // relationship with the users
    public function owner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }





}
