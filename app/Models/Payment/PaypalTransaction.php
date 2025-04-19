<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaypalTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'paypal_order_id',
        'status',
        'amount',
        'currency',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
