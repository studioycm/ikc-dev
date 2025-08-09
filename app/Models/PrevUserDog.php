<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // relationship with the dogs by dog Sagir id - dogs2users field is sagir_id, DogsDB field is SagirID
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
