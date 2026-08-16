# Laravel Invoice

A complete, professional, and production-ready invoice engine for Laravel. 

This package is designed for building robust invoice systems in e-commerce, POS, SaaS, or any other enterprise applications. It provides the full business logic out-of-the-box: invoice generation, precise tax/discount calculation, partial payments, state management, PDF generation, and public verification.

## Features

- **Fluent API**: Create invoices easily using the `InvoiceBuilder`.
- **Decimal-Safe Engine**: Financial amounts are stored and calculated using precise decimal operations to prevent floating-point errors.
- **Concurrent-Safe Numbering**: Auto-generates invoice sequences (e.g., `INV-2026-08-000001`) safely using DB transactions and locking.
- **Payment Tracking**: Track partial payments. Automatically transitions invoice statuses from `Pending` -> `Partially Paid` -> `Paid`.
- **Extensible Architecture**: Swap out the PDF Generator or Payment Gateway via Service Container bindings and Contracts.
- **REST API included**: Built-in API endpoints for fetching, paginating, downloading PDFs, and verifying QR Code tokens.
- **Fully Customizable**: Extend the underlying `Customer` model or override the provided Blade templates.

## Installation

You can install the package via composer:

```bash
composer require ardhikaxx/laravel-invoice
```

Publish the configuration file, migrations, and views:

```bash
php artisan vendor:publish --provider="Ardhikaxx\LaravelInvoice\InvoiceServiceProvider"
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

You can customize the package via `config/invoice.php`.
- **`prefix`** & **`number_format`**: Adjust how the invoice number is generated.
- **`currency`** & **`decimal_precision`**: Setup localization and rounding.
- **`company`**: Define the company details displayed on the PDF.
- **`models.customer`**: Bind your application's existing User/Customer model to the invoice system.

## Basic Usage

### Creating an Invoice

Use the `InvoiceBuilder` to quickly create an invoice with items.

```php
use Ardhikaxx\LaravelInvoice\Services\InvoiceBuilder;
use Ardhikaxx\LaravelInvoice\Models\Customer;

$customer = Customer::find(1);

$invoice = InvoiceBuilder::make()
    ->customer($customer)
    ->date(now())
    ->dueDate(now()->addDays(14))
    ->addItem('Web Development Services', 1, 5000000, ['tax' => 550000])
    ->addItem('Server Maintenance', 12, 150000)
    ->notes('Thank you for your business!')
    ->save();

echo $invoice->invoice_number; // e.g., INV-2026-08-000001
echo $invoice->grand_total; // 7,350,000.00
```

### Adding a Payment

The `PaymentService` automatically updates the invoice status when the balance is fulfilled.

```php
use Ardhikaxx\LaravelInvoice\Services\PaymentService;

$service = new PaymentService();

// Add a partial payment
$service->addPayment($invoice, 3000000, [
    'payment_method' => 'Bank Transfer',
    'notes' => 'First installment'
]);

// Invoice status is now `partially_paid`

// Pay the remainder
$service->addPayment($invoice, $invoice->due_amount);

// Invoice status is automatically transitioned to `paid`
// Event `InvoicePaid` is dispatched!
```

### Generating PDF

```php
use Ardhikaxx\LaravelInvoice\Contracts\PdfGeneratorInterface;

$pdfGenerator = app(PdfGeneratorInterface::class);

// Download directly (e.g., in a controller return)
return $pdfGenerator->download($invoice);

// Save to disk
$url = $pdfGenerator->save($invoice, 'public/invoices/inv-123.pdf');
```

## REST API Endpoints

Once installed, the following endpoints are available under the `api` middleware:
- `GET /api/invoices` - List invoices
- `GET /api/invoices/{uuid}` - Get specific invoice details
- `GET /api/invoices/{uuid}/pdf` - Download invoice PDF
- `GET /invoice/verify/{token}` - Public verification URL (ideal for QR Codes)

## Testing

```bash
composer test
```

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
