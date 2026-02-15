<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevClubUser extends Pivot
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'club2user';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'club_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'payment_status' => 'integer',
        'forbidden' => 'boolean',
        'expire_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'user_id', 'id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'club_id', 'id');
    }

    // isActive laravel attribute set/get methods, status = 'active' and expire_date >= now() and (payment_status = 1 or payment_status is null)
    function isActive(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => $attributes['status'] == 1
                && $attributes['expire_date'] >= now()
                && ($attributes['payment_status'] == 1 || $attributes['payment_status'] == null)
        );
    }
}
