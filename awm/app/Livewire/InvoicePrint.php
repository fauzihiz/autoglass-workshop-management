<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Component;

class InvoicePrint extends Component
{
    public Transaction $transaction;

    public function mount(Transaction $transaction): void
    {
        $this->transaction = $transaction->load([
            'customer',
            'vehicle.brand',
            'vehicle.model',
            'items.itemable',
            'payments',
            'items.serviceAssignment.technician',
        ]);
    }

    public function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.invoice-print', [
            'transaction' => $this->transaction,
        ])->layout('layouts.print');
    }
}