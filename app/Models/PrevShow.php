<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrevShow extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_prev';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ShowsDB';

    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
        'DataID' => 'integer',
        'ShowID' => 'integer',
        'MaxRegisters' => 'integer',
        'ShowType' => 'integer',
        'ClubID' => 'integer',
        'ShowStatus' => 'integer',
        'Check_all_members' => 'integer',
        'start_from_index' => 'integer',
        'ShowPrice' => 'integer',
        'Dog2Price1' => 'integer',
        'Dog2Price2' => 'integer',
        'Dog2Price3' => 'integer',
        'Dog2Price4' => 'integer',
        'Dog2Price5' => 'integer',
        'Dog2Price6' => 'integer',
        'Dog2Price7' => 'integer',
        'Dog2Price8' => 'integer',
        'Dog2Price9' => 'integer',
        'Dog2Price10' => 'integer',
        'CouplesPrice' => 'integer',
        'BGidulPrice' => 'integer',
        'ZezaimPrice' => 'integer',
        'YoungPrice' => 'integer',
        'MoreDogsPrice' => 'integer',
        'MoreDogsPrice2' => 'integer',
        'TicketCost' => 'integer',
        'PeototCost' => 'integer',
        'IsExtraTickets' => 'integer',
        'IsParking' => 'integer',
        'StartDate' => 'datetime',
        'EndDate' => 'datetime',
        'EndRegistrationDate' => 'datetime',
        'ModificationDateTime' => 'datetime',
        'CreationDateTime' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relations
    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'ClubID');
    }

    public function showClasses(): HasMany
    {
        return $this->hasMany(PrevShowClass::class, 'ShowID');
    }

    public function showArenas(): HasMany
    {
        return $this->hasMany(PrevShowArena::class, 'ShowID');
    }

    public function showArenasWithClasses(): HasMany
    {
        return $this->hasMany(PrevShowArena::class, 'ShowID')->with('showClasses');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(PrevShowRegistration::class, 'ShowID');
    }

    public function showDogs(): HasMany
    {
        return $this->hasMany(PrevShowDog::class, 'ShowID');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PrevShowResult::class, 'ShowID');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PrevShowPayment::class, 'ShowID');
    }

    public function breedEntries(): HasMany
    {
        return $this->hasMany(PrevShowBreed::class, 'ShowID');
    }

    // Scopes
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('ShowStatus', 1);
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->whereDate('StartDate', '>', now());
    }

    public function scopePast(Builder $q): Builder
    {
        return $q->whereDate('EndDate', '<', now());
    }

    public function scopeWithCountsForResource(Builder $q): Builder
    {
        return $q->withCount([
            'showArenas',
            'showClasses',
            'registrations',
            'showDogs',
            'results',
        ]);
    }
}
