<?php

namespace App\Livewire;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $methodFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingMethodFilter(): void
    {
        $this->resetPage();
    }

    public function getItems()
    {
        return Payment::query()
            ->with(['transaction.customer'])
            ->when($this->search !== '', function ($q) {
                $q->whereHas('transaction', function ($tq) {
                    $tq->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', function ($cq) {
                            $cq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->methodFilter !== '', function ($q) {
                $q->where('method', $this->methodFilter);
            })
            ->latest('paid_at')
            ->paginate(20);
    }

    public function getMethodOptions(): array
    {
        return [
            '' => 'All Methods',
            'cash' => 'Cash',
            'transfer' => 'Transfer',
            'qris' => 'QRIS',
            'other' => 'Other',
        ];
    }

    public function render()
    {
        return view('livewire.payment-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Payment History',
            'header' => 'Payment History',
        ]);
    }
}