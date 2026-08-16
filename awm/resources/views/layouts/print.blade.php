<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice - {{ $transaction->invoice_number ?? '' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; margin: 0; padding: 20px; }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 24px; border-bottom: 2px solid #1a1a1a; padding-bottom: 12px; }
        .invoice-title { font-size: 24px; font-weight: bold; }
        .invoice-meta { text-align: right; font-size: 11px; color: #555; }
        .section { margin-bottom: 16px; }
        .section-title { font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 4px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { font-weight: bold; font-size: 11px; text-transform: uppercase; color: #555; }
        .text-right { text-align: right; }
        .totals { margin-top: 16px; }
        .totals td { padding: 4px 8px; }
        .totals .total-row td { font-weight: bold; font-size: 14px; border-top: 2px solid #1a1a1a; }
        .footer { margin-top: 32px; font-size: 10px; color: #888; text-align: center; border-top: 1px solid #ddd; padding-top: 8px; }
        @media print { body { padding: 0; } }
    </style>
</head>
<body>
    @yield('content')
    <script>window.onload = function() { window.print(); };</script>
</body>
</html>