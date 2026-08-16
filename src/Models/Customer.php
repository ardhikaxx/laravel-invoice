<?php

namespace Ardhikaxx\LaravelInvoice\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;
    
    protected $table = 'invoice_customers';

    protected $guarded = ['id'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
