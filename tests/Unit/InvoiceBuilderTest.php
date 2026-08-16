<?php

namespace Vendor\LaravelInvoice\Tests\Unit;

use Vendor\LaravelInvoice\Tests\TestCase;
use Vendor\LaravelInvoice\Services\InvoiceBuilder;
use Vendor\LaravelInvoice\Models\Customer;
use Vendor\LaravelInvoice\Models\Invoice;

class InvoiceBuilderTest extends TestCase
{
    public function test_it_can_build_and_save_an_invoice()
    {
        $customer = Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $invoice = InvoiceBuilder::make()
            ->customer($customer)
            ->date(now())
            ->dueDate(now()->addDays(7))
            ->addItem('Test Item 1', 2, 500)
            ->addItem('Test Item 2', 1, 1000, ['discount' => 100])
            ->save();
            
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'customer_id' => $customer->id,
            'subtotal' => 2000, // (2*500) + (1*1000)
            'discount_amount' => 100,
            'grand_total' => 1900,
            'status' => 'draft',
        ]);
        
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'name' => 'Test Item 1',
            'quantity' => 2,
            'unit_price' => 500,
        ]);
        
        $this->assertCount(2, $invoice->items);
        $this->assertNotNull($invoice->invoice_number);
    }
}
