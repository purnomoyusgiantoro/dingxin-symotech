<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function dailyDeliveries(): HasMany
    {
        return $this->hasMany(DailyDelivery::class, 'created_by');
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'created_by');
    }

    public function cashDeposits(): HasMany
    {
        return $this->hasMany(CashDeposit::class, 'cashier_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, ['sales_admin', 'cashier', 'gm']);
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isSalesAdmin(): bool
    {
        return $this->role === 'sales_admin';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isGm(): bool
    {
        return $this->role === 'gm';
    }
}
