<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked' => 'boolean',
            'confirmed' => 'boolean',
        ];
    }

    public function regions() { return $this->belongsToMany(Region::class); }
    public function sites() { return $this->belongsToMany(Site::class); }
    public function preparedWeeklyReports() { return $this->hasMany(WeeklyReport::class, 'prepared_by'); }

    /** Only confirmed, non-blocked users may access the admin panel. */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->confirmed && ! $this->blocked;
    }
}
