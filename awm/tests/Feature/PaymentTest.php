<?php

use App\Enums\TransactionStatus;
use App\Models\Payment;

it('records a payment for a transaction', function () {
    $data = createGlassProductWithStock();
    $tx = createPendingTransaction($data);

    $tx->update(['status' => TransactionStatus::Confirmed->value]);

    Payment::create([
        'transaction_id' => $tx->id,
        'amount' => 500000,
        'method' => 'cash',
        'paid_at' => now(),
    ]);

    $tx->refresh();
    expect($tx->total_paid)->toBe(500000.0);
});

it('calculates balance due correctly', function () {
    $data = createGlassProductWithStock();
    $tx = createPendingTransaction($data);

    Payment::create([
        'transaction_id' => $tx->id,
        'amount' => 300000,
        'method' => 'cash',
        'paid_at' => now(),
    ]);

    $tx->refresh();
    $totalAmount = $tx->total_amount;
    $balanceDue = $tx->balance_due;
    expect($balanceDue)->toBe($totalAmount - 300000);
});

it('marks transaction as paid when fully paid', function () {
    $data = createGlassProductWithStock();
    $tx = createPendingTransaction($data);
    $totalAmount = $tx->total_amount;

    Payment::create([
        'transaction_id' => $tx->id,
        'amount' => $totalAmount,
        'method' => 'transfer',
        'paid_at' => now(),
    ]);

    $tx->refresh();
    expect($tx->is_paid)->toBeTrue();
});

it('stores payment method correctly', function () {
    $data = createGlassProductWithStock();
    $tx = createPendingTransaction($data);

    Payment::create([
        'transaction_id' => $tx->id,
        'amount' => 100000,
        'method' => 'qris',
        'reference_number' => 'QRIS-REF-001',
        'paid_at' => now(),
    ]);

    $payment = Payment::where('transaction_id', $tx->id)->first();
    expect($payment->method)->toBe('qris');
    expect($payment->reference_number)->toBe('QRIS-REF-001');
});
