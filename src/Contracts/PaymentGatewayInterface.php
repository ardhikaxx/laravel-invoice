<?php

namespace Ardhikaxx\LaravelInvoice\Contracts;

use Ardhikaxx\LaravelInvoice\Models\Invoice;

interface PaymentGatewayInterface
{
    /**
     * Generate a payment link or token for the given invoice.
     *
     * @param Invoice $invoice
     * @return string The URL or Token for the payment
     */
    public function generatePaymentLink(Invoice $invoice): string;

    /**
     * Process a callback/webhook from the payment gateway.
     *
     * @param array $payload
     * @return bool True if successful, false otherwise
     */
    public function processCallback(array $payload): bool;
    
    /**
     * Check the current status of a payment directly from the gateway.
     */
    public function checkStatus(string $transactionReference): string;
}
