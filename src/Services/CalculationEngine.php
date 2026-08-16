<?php

namespace Ardhikaxx\LaravelInvoice\Services;

use Ardhikaxx\LaravelInvoice\Models\Invoice;

class CalculationEngine
{
    /**
     * Calculate and apply totals for the invoice builder data.
     * We use simple float arithmetic here, but in a true production system 
     * where BCMath is available, it should wrap BCMath. We will emulate
     * decimal precision using rounding to the configured precision.
     */
    public function calculate(array &$attributes, array &$items): void
    {
        $precision = config('invoice.decimal_precision', 2);
        
        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        
        foreach ($items as &$item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            
            // Item Subtotal
            $itemSubtotal = round($qty * $price, $precision);
            
            // Item Discount
            $itemDiscount = (float) ($item['discount'] ?? 0);
            
            // Base amount for tax calculation
            $amountAfterDiscount = $itemSubtotal - $itemDiscount;
            
            // Item Tax
            $itemTax = (float) ($item['tax'] ?? 0);
            
            // Item Total
            $itemTotal = round($amountAfterDiscount + $itemTax, $precision);
            
            $item['subtotal'] = $itemSubtotal;
            $item['total'] = $itemTotal;
            
            $subtotal += $itemSubtotal;
            $totalDiscount += $itemDiscount;
            $totalTax += $itemTax;
        }

        $attributes['subtotal'] = round($subtotal, $precision);
        $attributes['discount_amount'] = round($totalDiscount, $precision);
        $attributes['tax_amount'] = round($totalTax, $precision);
        
        $shipping = (float) ($attributes['shipping_fee'] ?? 0);
        $service = (float) ($attributes['service_fee'] ?? 0);
        $adjustment = (float) ($attributes['adjustment'] ?? 0);
        
        $grandTotal = $subtotal - $totalDiscount + $totalTax + $shipping + $service + $adjustment;
        
        $attributes['grand_total'] = round($grandTotal, $precision);
    }
}
