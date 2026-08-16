<?php

use Illuminate\Support\Facades\Route;
use Ardhikaxx\LaravelInvoice\Http\Controllers\InvoiceController;

Route::prefix('api/invoices')->middleware('api')->group(function () {
    Route::get('/', [InvoiceController::class, 'index']);
    Route::get('/{uuid}', [InvoiceController::class, 'show']);
    Route::get('/{uuid}/pdf', [InvoiceController::class, 'downloadPdf']);
});

// Public verification route
Route::get('invoice/verify/{token}', [InvoiceController::class, 'verify'])->name('invoice.verify');
