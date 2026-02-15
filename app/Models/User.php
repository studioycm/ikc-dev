<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasPanelShield, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'prev_user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'prev_user_id' => 'integer',
        ];
    }

    /**
     * Checks if the user has access to the panel.
     *
     * This function verifies if the user has the necessary permissions to access the panel.
     * It checks if the user is logged in and if their role allows access to the panel.
     *
     * @param Panel $panel The panel object to check access for.
     * @return bool Returns true if the user has access to the panel, false otherwise.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // can access panel by isSuperAdmin or by isPanelUser, and using Filament Shield plugin.
        // match by $panel->getId(), 'admin' only by isSuperAdmin, 'user' by isPanelUser. isSuperAdmin can access all panels.


        return $this->isSuperAdmin() ? true : $this->isPanelUser();

    }

    /**
     * check if user is shild super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * check if user is shild panel user
     */
    public function isPanelUser(): bool
    {
        return $this->hasRole('panel_user');
    }

    public function prevUser(): BelongsTo
    {
        return $this->belongsTo(PrevUser::class, 'prev_user_id', 'id');
    }

    public function prevDogs(): BelongsToMany
    {
        return $this->belongsToMany(PrevDog::class, 'dogs2users', 'user_id', 'sagir_id', 'prev_user_id', 'SagirID')
            ->withTimestamps()
            ->using(PrevUserDog::class)
            ->as('ownership')
            ->withPivot('status', 'created_at', 'updated_at', 'deleted_at')
            ->wherePivot('deleted_at', null)
            ->wherePivot('status', 'current');
    }

    public function getFilamentName(): string
    {
        return "{$this->name}";
    }
}
