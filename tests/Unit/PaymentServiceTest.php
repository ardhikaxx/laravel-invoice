<?php

namespace Vendor\LaravelInvoice\Tests\Unit;

use Vendor\LaravelInvoice\Tests\TestCase;
use Vendor\LaravelInvoice\Services\PaymentService;
use Vendor\LaravelInvoice\Models\Invoice;
use Vendor\LaravelInvoice\Enums\InvoiceStatus;
use Vendor\LaravelInvoice\Events\InvoicePaid;
use Illuminate\Support\Facades\Event;

class PaymentServiceTest extends TestCase
{
    public function test_it_adds_payment_and_updates_status_to_partially_paid()
    {
        $invoice = Invoice::create([
            'uuid' => 'test-uuid',
            'invoice_number' => 'INV-1',
            'grand_total' => 1000,
            'status' => InvoiceStatus::PENDING,
            'invoice_date' => now(),
        ]);
        
        $service = new PaymentService();
        $payment = $service->addPayment($invoice, 400);
        
        $this->assertEquals(400, $payment->amount);
        $this->assertEquals(400, $invoice->fresh()->paid_amount);
        $this->assertEquals(InvoiceStatus::PARTIALLY_PAID, $invoice->fresh()->status);
    }
    
    public function test_it_updates_status_to_paid_and_fires_event_when_fully_paid()
    {
        Event::fake();
        
        $invoice = Invoice::create([
            'uuid' => 'test-uuid',
            'invoice_number' => 'INV-2',
            'grand_total' => 1000,
            'status' => InvoiceStatus::PENDING,
            'invoice_date' => now(),
        ]);
        
        $service = new PaymentService();
        $service->addPayment($invoice, 1000); // Pay in full
        
        $this->assertEquals(1000, $invoice->fresh()->paid_amount);
        $this->assertEquals(InvoiceStatus::PAID, $invoice->fresh()->status);
        
        Event::assertDispatched(InvoicePaid::class, function ($event) use ($invoice) {
            return $event->invoice->id === $invoice->id;
        });
    }
}
