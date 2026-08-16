<?php

namespace Vendor\LaravelInvoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InvoiceItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:4',
        'discount' => 'decimal:4',
        'tax' => 'decimal:4',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
