<?php

namespace App\Models;

use App\Enums\Legacy\LegacyUserRequestChampionType;
use App\Enums\Legacy\LegacyUserRequestPaperType;
use App\Enums\Legacy\LegacyUserRequestTopic;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PrevUserRequest extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    protected $table = 'public_registration';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'topic' => LegacyUserRequestTopic::class,
        'club_id' => 'integer',
        'approve_1' => 'boolean',
        'approve_2' => 'boolean',
        'approve_3' => 'boolean',
        'year' => 'integer',
        'birth_date' => 'date',
        'dog1_vaccine_date' => 'date',
        'dog2_vaccine_date' => 'date',
        'dog3_vaccine_date' => 'date',
        'payment_incerments' => 'integer',
        'total_amount' => 'integer',
        'record_date_time' => 'datetime',
        'payment_date_time' => 'datetime',
        'sagirID' => 'integer',
        'IsDone' => 'boolean',
        'DoneByUserID' => 'integer',
        'DoneDate' => 'datetime',
        'owner_id' => 'integer',
        'agra_city' => 'integer',
        'shipping_type_id' => 'integer',
        'shipping' => 'string',
        'champion_certificate_type' => LegacyUserRequestChampionType::class,
        'paper_request_type' => LegacyUserRequestPaperType::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'club_id', 'id');
    }

    public function userByPhone(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'mobile_phone', 'mobile_phone');
    }

    public function dog(): BelongsTo
    {
        return $this->belongsTo(PrevDog::class, 'sagirID', 'SagirID');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'owner_id', 'id');
    }

    public function doneBy(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'DoneByUserID', 'id');
    }

    public function vetAuth(): BelongsTo
    {
        return $this->belongsTo(PrevVetAuth::class, 'agra_city', 'id');
    }

    // attribute accessors for status to lower case writen in new Laravel 12 format:
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn() => strtolower($this->attributes['status']),
            set: fn($value) => Str::title($value),
        );
    }
}
