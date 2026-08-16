<?php

namespace Ardhikaxx\LaravelInvoice\Commands;

use Illuminate\Console\Command;
use Ardhikaxx\LaravelInvoice\Services\InvoiceBuilder;
use Ardhikaxx\LaravelInvoice\Models\Customer;

class MakeInvoiceCommand extends Command
{
    protected $signature = 'invoice:create {--customer_id= : The ID of the customer} {--amount=1000 : Default item amount}';

    protected $description = 'Quickly generate a mock invoice for testing purposes';

    public function handle()
    {
        $this->info('Generating mock invoice...');
        
        $customerId = $this->option('customer_id');
        $amount = (float) $this->option('amount');

        $builder = InvoiceBuilder::make()
            ->date(now())
            ->dueDate(now()->addDays(7))
            ->addItem('Mock Service Item', 1, $amount, ['tax' => $amount * 0.11])
            ->notes('This is a mock invoice generated via Artisan console.');
            
        if ($customerId && class_exists(Customer::class)) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $builder->customer($customer);
            }
        }

        $invoice = $builder->save();

        $this->info('Invoice created successfully!');
        $this->table(
            ['Invoice Number', 'Grand Total', 'Status'],
            [[$invoice->invoice_number, $invoice->grand_total, $invoice->status->name]]
        );

        return 0;
    }
}

