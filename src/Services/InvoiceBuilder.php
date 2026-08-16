<?php

namespace Ardhikaxx\LaravelInvoice\Services;

use Ardhikaxx\LaravelInvoice\Models\Invoice;
use Ardhikaxx\LaravelInvoice\Models\Customer;
use Ardhikaxx\LaravelInvoice\Enums\InvoiceStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InvoiceBuilder
{
    protected array $attributes = [];
    protected array $items = [];
    protected ?Customer $customer = null;

    public static function make(): self
    {
        return new self();
    }

    public function customer(Customer $customer): self
    {
        $this->customer = $customer;
        $this->attributes['customer_id'] = $customer->id;
        return $this;
    }

    public function date(\DateTimeInterface|string $date): self
    {
        $this->attributes['invoice_date'] = $date;
        return $this;
    }

    public function dueDate(\DateTimeInterface|string $date): self
    {
        $this->attributes['due_date'] = $date;
        return $this;
    }

    public function addItem(string $name, float $quantity, float $unitPrice, array $options = []): self
    {
        $this->items[] = array_merge([
            'name' => $name,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ], $options);
        
        return $this;
    }

    public function currency(string $currency): self
    {
        $this->attributes['currency'] = $currency;
        return $this;
    }

    public function notes(string $notes): self
    {
        $this->attributes['notes'] = $notes;
        return $this;
    }
    
    public function status(InvoiceStatus $status): self
    {
        $this->attributes['status'] = $status;
        return $this;
    }

    public function save(): Invoice
    {
        // Setup defaults
        $this->attributes['uuid'] ??= (string) Str::uuid();
        $this->attributes['status'] ??= InvoiceStatus::DRAFT;
        
        return DB::transaction(function () {
            $this->attributes['invoice_number'] ??= app(InvoiceNumberGenerator::class)->generate();
            
            // Execute calculations
            app(CalculationEngine::class)->calculate($this->attributes, $this->items);

            $invoice = Invoice::create($this->attributes);

            foreach ($this->items as $item) {
                $invoice->items()->create($item);
            }

            return $invoice;
        });
    }
}
