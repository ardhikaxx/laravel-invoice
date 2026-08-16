<?php

namespace Vendor\LaravelInvoice\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Vendor\LaravelInvoice\Models\Invoice;

class InvoicePaid
{
    use Dispatchable, SerializesModels;

    public Invoice $invoice;

    /**
     * Create a new event instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
