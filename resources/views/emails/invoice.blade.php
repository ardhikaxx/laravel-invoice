<!DOCTYPE html>
<html>
<head>
    <title>Your Invoice</title>
</head>
<body>
    <h2>Hello {{ $invoice->customer->name ?? 'Valued Customer' }},</h2>
    
    <p>Thank you for your business. Attached to this email is your invoice <strong>{{ $invoice->invoice_number }}</strong>.</p>
    
    <p>
        <strong>Issue Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}<br>
        <strong>Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '-' }}<br>
        <strong>Total Amount:</strong> {{ number_format($invoice->grand_total, 2) }} {{ config('invoice.currency', 'IDR') }}
    </p>

    <p>If you have any questions concerning this invoice, please contact us.</p>
    
    <p>Best regards,<br>
    {{ config('invoice.company.name', config('app.name')) }}</p>
</body>
</html>
