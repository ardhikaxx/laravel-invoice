<?php

namespace Vendor\LaravelInvoice\Services\Pdf;

use Vendor\LaravelInvoice\Contracts\PdfGeneratorInterface;
use Vendor\LaravelInvoice\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class DompdfGenerator implements PdfGeneratorInterface
{
    protected $pdf;

    public function __construct()
    {
        // Assuming barryvdh/laravel-dompdf is installed or we use the underlying Dompdf class
        // $this->pdf = app('dompdf.wrapper');
    }

    public function generate(Invoice $invoice): string
    {
        // Load the view
        $template = config('invoice.pdf.template', 'invoice::templates.classic');
        
        $html = View::make($template, [
            'invoice' => $invoice,
            'company' => config('invoice.company'),
        ])->render();
        
        // This is a stub for dompdf generation
        // $this->pdf->loadHTML($html);
        // return $this->pdf->output();
        
        // For the sake of this package implementation without immediate dependencies:
        return "PDF Content (stub) for Invoice {$invoice->invoice_number}\n\n" . $html;
    }

    public function download(Invoice $invoice, string $filename = null)
    {
        $filename = $filename ?? "invoice-{$invoice->invoice_number}.pdf";
        
        // $this->pdf->loadHTML($this->getViewHtml($invoice));
        // return $this->pdf->download($filename);
        
        return response($this->generate($invoice))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function save(Invoice $invoice, string $path = null): string
    {
        $disk = config('invoice.pdf.storage_disk', 'local');
        $path = $path ?? "invoices/{$invoice->invoice_number}.pdf";
        
        Storage::disk($disk)->put($path, $this->generate($invoice));
        
        return Storage::disk($disk)->url($path);
    }
}
