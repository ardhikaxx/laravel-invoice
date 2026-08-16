<?php

namespace Ardhikaxx\LaravelInvoice;

use Illuminate\Foundation\Application;

class InvoiceManager
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    // Core manager logic for invoice creation and facade binding
}
