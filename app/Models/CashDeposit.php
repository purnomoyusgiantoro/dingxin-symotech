<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'date',
        'amount_received',
        'deposit_phase',
        'cashier_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount_received' => 'decimal:2',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
