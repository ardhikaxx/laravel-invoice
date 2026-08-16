<?php

namespace Vendor\LaravelInvoice\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vendor\LaravelInvoice\Models\Invoice;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $invoice;
    public $event;
    public $webhookUrl;
    public $secret;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    public function __construct(Invoice $invoice, string $event, string $webhookUrl, ?string $secret = null)
    {
        $this->invoice = $invoice;
        $this->event = $event;
        $this->webhookUrl = $webhookUrl;
        $this->secret = $secret;
    }

    public function handle(): void
    {
        $payload = [
            'event' => $this->event,
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'invoice_number' => $this->invoice->invoice_number,
                'status' => $this->invoice->status,
                'grand_total' => $this->invoice->grand_total,
                'uuid' => $this->invoice->uuid,
            ]
        ];

        $jsonPayload = json_encode($payload);
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->secret) {
            // Create HMAC SHA-256 signature
            $signature = hash_hmac('sha256', $jsonPayload, $this->secret);
            $headers['X-Invoice-Signature'] = $signature;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($this->webhookUrl, $payload);
                
            if ($response->failed()) {
                Log::warning("Webhook failed for invoice {$this->invoice->invoice_number}. Status: {$response->status()}");
                $this->release(60); // Exponential backoff in production
            }
        } catch (\Exception $e) {
            Log::error("Webhook exception for invoice {$this->invoice->invoice_number}: " . $e->getMessage());
            $this->release(60);
        }
    }
}
