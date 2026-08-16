<?php

namespace Vendor\LaravelInvoice\Services;

use Vendor\LaravelInvoice\Models\Invoice;
use Vendor\LaravelInvoice\Models\Payment;
use Vendor\LaravelInvoice\Enums\InvoiceStatus;
use Vendor\LaravelInvoice\Enums\PaymentStatus;
use Vendor\LaravelInvoice\Events\InvoicePaid;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Add a payment to an invoice and automatically update its status.
     */
    public function addPayment(Invoice $invoice, float $amount, array $options = []): Payment
    {
        return DB::transaction(function () use ($invoice, $amount, $options) {
            $payment = $invoice->payments()->create([
                'amount' => $amount,
                'currency' => $options['currency'] ?? $invoice->currency,
                'payment_method' => $options['payment_method'] ?? null,
                'transaction_reference' => $options['transaction_reference'] ?? null,
                'status' => PaymentStatus::COMPLETED,
                'paid_at' => $options['paid_at'] ?? now(),
                'gateway' => $options['gateway'] ?? null,
                'notes' => $options['notes'] ?? null,
                'metadata' => $options['metadata'] ?? null,
            ]);

            // Recalculate paid amount
            $totalPaid = $invoice->payments()
                ->where('status', PaymentStatus::COMPLETED)
                ->sum('amount');
                
            $invoice->paid_amount = $totalPaid;
            
            // Check if fully paid
            if ($totalPaid >= $invoice->grand_total) {
                $invoice->status = InvoiceStatus::PAID;
                $invoice->save();
                
                event(new InvoicePaid($invoice));
            } else {
                $invoice->status = InvoiceStatus::PARTIALLY_PAID;
                $invoice->save();
            }

            return $payment;
        });
    }
}
