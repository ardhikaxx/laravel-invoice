<?php

namespace Vendor\LaravelInvoice\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Vendor\LaravelInvoice\Models\Invoice;
use Vendor\LaravelInvoice\Services\InvoiceBuilder;
use Vendor\LaravelInvoice\Services\PaymentService;
use Vendor\LaravelInvoice\Contracts\PdfGeneratorInterface;

class InvoiceController extends Controller
{
    /**
     * Get a list of invoices.
     */
    public function index(Request $request): JsonResponse
    {
        // Simple listing with pagination
        $invoices = Invoice::with(['items', 'customer'])
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 15));
            
        return response()->json([
            'success' => true,
            'data' => $invoices->items(),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'total' => $invoices->total()
            ]
        ]);
    }

    /**
     * Show a specific invoice.
     */
    public function show(string $uuid): JsonResponse
    {
        $invoice = Invoice::where('uuid', $uuid)->with(['items', 'payments', 'customer'])->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }
    
    /**
     * Download the invoice PDF.
     */
    public function downloadPdf(string $uuid, PdfGeneratorInterface $pdfGenerator)
    {
        $invoice = Invoice::where('uuid', $uuid)->with(['items', 'customer'])->firstOrFail();
        return $pdfGenerator->download($invoice);
    }

    /**
     * Public verification endpoint for QR code validation.
     */
    public function verify(string $token): JsonResponse
    {
        $invoice = Invoice::where('verification_token', $token)
            ->with(['customer', 'items'])
            ->first();
            
        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired invoice token.'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'grand_total' => $invoice->grand_total,
                'due_date' => $invoice->due_date,
                'issued_at' => $invoice->invoice_date,
                'merchant' => config('invoice.company.name'),
                'is_valid' => true
            ]
        ]);
    }
}
