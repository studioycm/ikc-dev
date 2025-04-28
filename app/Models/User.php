<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;



class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasPanelShield, HasRoles;

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
        // return str_ends_with($this->email, '+admin@gmail.com'); // @todo Change this to check for access level
        // is super admin by shild plugin 
        
        return $this->isSuperAdmin() ? true : $this->isPanelUser();


        
    }

    /**
     * check if user is shild super admin 
     * 
     * 
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * check if user is shild panel user 
     * 
     * 
     * @return bool
     */
    public function isPanelUser(): bool
    {
        return $this->hasRole('panel_user');
    }

    public function getFilamentName(): string
    {
        return "{$this->name}";
    }

}
