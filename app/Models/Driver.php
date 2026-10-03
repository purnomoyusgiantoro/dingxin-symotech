<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_code',
        'area_code',
        'phone_number',
        'plate_number',
        'is_active',
        'cumulative_balance',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'cumulative_balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyDeliveries(): HasMany
    {
        return $this->hasMany(DailyDelivery::class);
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function transferPayments(): HasMany
    {
        return $this->hasMany(TransferPayment::class);
    }

    public function creditDeliveries(): HasMany
    {
        return $this->hasMany(CreditDelivery::class);
    }

    public function cashDeposits(): HasMany
    {
        return $this->hasMany(CashDeposit::class);
    }

    public function dailySettlements(): HasMany
    {
        return $this->hasMany(DailySettlement::class);
    }
}
