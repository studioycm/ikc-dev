<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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


    protected $casts = [
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

    protected function ShowType(): Attribute
    {
        return Attribute::make(
            get: function ($value): ?string {
                $code = (int) ($value ?? 0);

                return match ($code) {
                    1 => __('International Show'),
                    2 => __('Clubs Show'),
                    3 => __('National Show'),
                    4 => __('Breeding Qualification Test'),
                    default => __('Not set'),
                };
            }
        );
    }




    // Relations
    public function club(): BelongsTo
    {
        return $this->belongsTo(PrevClub::class, 'ClubID', 'id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(PrevShowClass::class, 'ShowID', 'id');
    }

    public function arenas(): HasMany
    {
        return $this->hasMany(PrevShowArena::class, 'ShowID', 'id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(PrevShowRegistration::class, 'ShowID', 'id');
    }

    public function showDogs(): HasMany
    {
        return $this->hasMany(PrevShowDog::class, 'ShowID', 'id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(PrevShowResult::class, 'ShowID', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PrevShowPayment::class, 'ShowID', 'id');
    }

    public function breeds(): HasMany
    {
        return $this->hasMany(PrevShowBreed::class, 'ShowID', 'id');
    }

    // Scopes
    #[Scope]
    protected function activeShow (Builder $q): void {

        $q->where('ShowStatus', '=', 2);
    }

    #[Scope]
    protected function upcoming(Builder $q): void
    {
        $q->whereDate('StartDate', '>', now());
    }

    #[Scope]
    protected function past(Builder $q): void
    {
        $q->whereDate('EndDate', '<', now());
    }


    public function scopeWithCountsForResource(Builder $q): Builder
    {
        return $q->withCount([
            'arenas',
            'classes',
            'registrations',
            'showDogs',
            'results',
        ]);
    }
}
