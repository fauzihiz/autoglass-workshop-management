@extends('layouts.print')

@section('content')
    <div class="invoice-header">
        <div>
            <div class="invoice-title">INVOICE</div>
            <div style="font-size: 11px; color: #888;">AutoGlass Workshop</div>
        </div>
        <div class="invoice-meta">
            <div><strong>{{ $transaction->invoice_number }}</strong></div>
            <div>{{ $transaction->created_at->format('d M Y H:i') }}</div>
            <div>Status: {{ ucfirst($transaction->status) }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Customer</div>
        <div><strong>{{ $transaction->customer->name ?? '—' }}</strong></div>
        @if ($transaction->customer->phone)
            <div>Phone: {{ $transaction->customer->phone }}</div>
        @endif
        @if ($transaction->customer->email)
            <div>Email: {{ $transaction->customer->email }}</div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Vehicle</div>
        <div>
            {{ $transaction->vehicle->license_plate ?? '—' }} —
            {{ ($transaction->vehicle->brand->name ?? '') . ' ' . ($transaction->vehicle->model->name ?? '') }}
        </div>
        @if ($transaction->vehicle->year)
            <div>Year: {{ $transaction->vehicle->year }} @if ($transaction->vehicle->color) | Color: {{ $transaction->vehicle->color }} @endif</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Type</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->items as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $item->itemable->name ?? '—' }}</td>
                    <td>{{ str_contains($item->itemable_type, 'Glass') ? 'Glass' : 'Service' }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($item->total_price, 0, ',', '.') }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table style="width: 300px; margin-left: auto;">
            <tr><td>Subtotal</td><td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td></tr>
            @if ($transaction->glass_cost > 0)
                <tr><td>Glass Cost</td><td class="text-right">Rp {{ number_format($transaction->glass_cost, 0, ',', '.') }}</td></tr>
            @endif
            @if ($transaction->profit > 0)
                <tr><td>Profit</td><td class="text-right" style="color: #16a34a;">Rp {{ number_format($transaction->profit, 0, ',', '.') }}</td></tr>
            @endif
            <tr><td>Paid</td><td class="text-right" style="color: #16a34a;">Rp {{ number_format($transaction->total_paid, 0, ',', '.') }}</td></tr>
            <tr class="total-row"><td>Balance Due</td><td class="text-right">Rp {{ number_format($transaction->balance_due, 0, ',', '.') }}</td></tr>
        </table>
    </div>

    @if ($transaction->payments->isNotEmpty())
        <div class="section" style="margin-top: 24px;">
            <div class="section-title">Payments</div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->payments as $payment)
                        <tr>
                            <td>{{ $payment->paid_at?->format('d M Y H:i') }}</td>
                            <td>{{ ucfirst($payment->method) }}</td>
                            <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        Thank you for your business! — AutoGlass Workshop
    </div>
@endsection