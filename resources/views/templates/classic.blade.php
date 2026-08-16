<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ trans('invoice::invoice.invoice') }} {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table td { padding: 5px; vertical-align: top; }
        table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        table tr.item td { border-bottom: 1px solid #eee; }
        table tr.item.last td { border-bottom: none; }
        table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <h2>{{ $company['name'] ?? 'Company' }}</h2>
                            </td>
                            <td>
                                {{ trans('invoice::invoice.invoice_number') }}: {{ $invoice->invoice_number }}<br>
                                {{ trans('invoice::invoice.created_date') }}: {{ $invoice->invoice_date->format(config('invoice.date_format', 'd/m/Y')) }}<br>
                                {{ trans('invoice::invoice.due_date') }}: {{ $invoice->due_date ? $invoice->due_date->format(config('invoice.date_format', 'd/m/Y')) : '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>{{ trans('invoice::invoice.item') }}</td>
                <td>{{ trans('invoice::invoice.price') }}</td>
            </tr>
            
            @foreach($invoice->items as $item)
            <tr class="item">
                <td>{{ $item->name }} (x{{ $item->quantity }})</td>
                <td>{{ config('invoice.currency_symbol') }} {{ number_format($item->total, config('invoice.decimal_precision', 2), config('invoice.decimal_separator', ','), config('invoice.thousand_separator', '.')) }}</td>
            </tr>
            @endforeach
            
            <tr class="total">
                <td></td>
                <td>
                   {{ trans('invoice::invoice.grand_total') }}: {{ config('invoice.currency_symbol') }} {{ number_format($invoice->grand_total, config('invoice.decimal_precision', 2), config('invoice.decimal_separator', ','), config('invoice.thousand_separator', '.')) }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
