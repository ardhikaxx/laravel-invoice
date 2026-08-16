<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invoice Numbering Configuration
    |--------------------------------------------------------------------------
    */
    'prefix' => 'INV-',
    'number_format' => 'INV-{YEAR}-{MONTH}-{SEQUENCE}',
    'sequence_length' => 6,
    
    /*
    |--------------------------------------------------------------------------
    | Currency & Formatting
    |--------------------------------------------------------------------------
    */
    'currency' => 'IDR',
    'currency_symbol' => 'Rp',
    'decimal_precision' => 2,
    'thousand_separator' => '.',
    'decimal_separator' => ',',
    
    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    'tax_behavior' => 'exclusive', // exclusive, inclusive
    'discount_behavior' => 'before_tax', // before_tax, after_tax
    'expiration_days' => 14,
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'date_format' => 'd/m/Y',
    
    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    */
    'company' => [
        'name' => 'My Company',
        'address' => 'Jl. Jend. Sudirman No. 1, Jakarta',
        'email' => 'billing@example.com',
        'phone' => '+62 811 1234 5678',
        'tax_id' => '01.234.567.8-091.000',
        'logo' => null,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | PDF Generation
    |--------------------------------------------------------------------------
    */
    'pdf' => [
        'driver' => 'dompdf', // or other supported drivers
        'template' => 'invoice::templates.classic',
        'storage_disk' => 'local',
        'paper_size' => 'A4',
        'orientation' => 'portrait',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    */
    'models' => [
        'customer' => \Ardhikaxx\LaravelInvoice\Models\Customer::class,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Queues & Webhooks
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'enabled' => true,
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'queue' => 'default',
    ],
    
];
