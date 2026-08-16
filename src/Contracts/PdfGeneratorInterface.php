<?php

namespace Ardhikaxx\LaravelInvoice\Contracts;

use Ardhikaxx\LaravelInvoice\Models\Invoice;

interface PdfGeneratorInterface
{
    /**
     * Generate PDF for the given invoice.
     *
     * @param Invoice $invoice
     * @return string The raw PDF content
     */
    public function generate(Invoice $invoice): string;

    /**
     * Download the PDF invoice.
     */
    public function download(Invoice $invoice, string $filename = null);

    /**
     * Save the PDF invoice to the configured storage disk.
     */
    public function save(Invoice $invoice, string $path = null): string;
}
