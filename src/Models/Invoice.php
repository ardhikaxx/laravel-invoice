<?php

namespace Ardhikaxx\LaravelInvoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ardhikaxx\LaravelInvoice\Enums\InvoiceStatus;

class Invoice extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'shipping_fee' => 'decimal:4',
        'service_fee' => 'decimal:4',
        'adjustment' => 'decimal:4',
        'grand_total' => 'decimal:4',
        'paid_amount' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(config('invoice.models.customer', Customer::class));
    }

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function getDueAmountAttribute(): float
    {
        return max(0, $this->grand_total - $this->paid_amount);
    }
}
