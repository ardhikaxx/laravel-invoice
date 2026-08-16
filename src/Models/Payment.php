<?php

namespace Vendor\LaravelInvoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\LaravelInvoice\Enums\PaymentStatus;

class Payment extends Model
{
    protected $table = 'invoice_payments';

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:4',
        'paid_at' => 'datetime',
        'status' => PaymentStatus::class,
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
