<?php

namespace Ardhikaxx\LaravelInvoice\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ardhikaxx\LaravelInvoice\Models\Invoice;
use Ardhikaxx\LaravelInvoice\Contracts\PdfGeneratorInterface;

class InvoiceCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build(PdfGeneratorInterface $pdfGenerator)
    {
        $pdfContent = $pdfGenerator->generate($this->invoice);
        
        $filename = 'Invoice_' . $this->invoice->invoice_number . '.pdf';

        return $this->subject('Invoice ' . $this->invoice->invoice_number)
                    ->view('invoice::emails.invoice')
                    ->attachData($pdfContent, $filename, [
                        'mime' => 'application/pdf',
                    ]);
    }
}
