<?php

namespace Vendor\LaravelInvoice\Tests\Unit;

use Vendor\LaravelInvoice\Tests\TestCase;
use Vendor\LaravelInvoice\Services\CalculationEngine;

class CalculationEngineTest extends TestCase
{
    public function test_it_calculates_subtotal_and_grand_total_correctly()
    {
        $engine = new CalculationEngine();
        
        $attributes = [];
        $items = [
            [
                'quantity' => 2,
                'unit_price' => 1000.50,
                'discount' => 0,
                'tax' => 0,
            ],
            [
                'quantity' => 1,
                'unit_price' => 500,
                'discount' => 50,
                'tax' => 45, // Tax applied after discount
            ]
        ];
        
        $engine->calculate($attributes, $items);
        
        // Item 1: 2 * 1000.50 = 2001
        $this->assertEquals(2001.00, $items[0]['subtotal']);
        $this->assertEquals(2001.00, $items[0]['total']);
        
        // Item 2: 1 * 500 = 500. Subtotal = 500. Total = 500 - 50 + 45 = 495
        $this->assertEquals(500.00, $items[1]['subtotal']);
        $this->assertEquals(495.00, $items[1]['total']);
        
        // Attributes check
        $this->assertEquals(2501.00, $attributes['subtotal']);
        $this->assertEquals(50.00, $attributes['discount_amount']);
        $this->assertEquals(45.00, $attributes['tax_amount']);
        
        // Grand Total: 2501 - 50 + 45 = 2496
        $this->assertEquals(2496.00, $attributes['grand_total']);
    }
    
    public function test_it_includes_fees_in_grand_total()
    {
        $engine = new CalculationEngine();
        
        $attributes = [
            'shipping_fee' => 100,
            'service_fee' => 50,
            'adjustment' => -25,
        ];
        
        $items = [
            [
                'quantity' => 1,
                'unit_price' => 1000,
            ]
        ];
        
        $engine->calculate($attributes, $items);
        
        // Subtotal: 1000. Grand Total: 1000 + 100 + 50 - 25 = 1125
        $this->assertEquals(1125.00, $attributes['grand_total']);
    }
}
