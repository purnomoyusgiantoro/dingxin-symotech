<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'date',
        'amount_carried',
        'amount_returned',
        'amount_transfer_approved',
        'amount_credit',
        'target_cash',
        'actual_cash',
        'difference',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount_carried' => 'decimal:2',
            'amount_returned' => 'decimal:2',
            'amount_transfer_approved' => 'decimal:2',
            'amount_credit' => 'decimal:2',
            'target_cash' => 'decimal:2',
            'actual_cash' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
