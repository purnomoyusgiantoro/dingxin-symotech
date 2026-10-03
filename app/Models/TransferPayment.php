<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'date',
        'store_name',
        'claimed_amount',
        'verified_amount',
        'proof_image_path',
        'status',
        'verified_by',
        'verified_at',
        'notes',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'claimed_amount' => 'decimal:2',
            'verified_amount' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
