<?php

namespace Vendor\LaravelInvoice\Services;

use Vendor\LaravelInvoice\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceNumberGenerator
{
    /**
     * Safely generates an invoice number preventing race conditions using a DB transaction.
     */
    public function generate(): string
    {
        return DB::transaction(function () {
            // Lock the last inserted invoice for the sequence to prevent race conditions
            $lastInvoice = Invoice::lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();
                
            $sequence = 1;
            
            if ($lastInvoice && $lastInvoice->invoice_number) {
                // Extract sequence from the last invoice number. 
                // Assuming format like 'INV-YYYY-MM-XXXXXX'
                $parts = explode('-', $lastInvoice->invoice_number);
                $lastSequence = (int) end($parts);
                $sequence = $lastSequence + 1;
            }

            $prefix = config('invoice.prefix', 'INV-');
            $length = config('invoice.sequence_length', 6);
            $year = date('Y');
            $month = date('m');
            
            $format = config('invoice.number_format', 'INV-{YEAR}-{MONTH}-{SEQUENCE}');
            
            $number = str_replace(
                ['{YEAR}', '{MONTH}', '{SEQUENCE}'],
                [$year, $month, str_pad((string) $sequence, $length, '0', STR_PAD_LEFT)],
                $format
            );
            
            // Fallback prefix if format doesn't contain placeholders
            if (!str_contains($format, '{SEQUENCE}')) {
                $number = $prefix . date('Y-m') . '-' . str_pad((string) $sequence, $length, '0', STR_PAD_LEFT);
            }
            
            return $number;
        });
    }
}
