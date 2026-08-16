<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $typeFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function getItems()
    {
        return Transaction::query()
            ->with(['customer', 'items'])
            ->status($this->statusFilter ?: null)
            ->type($this->typeFilter ?: null)
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sq) {
                    $sq->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhereHas('vehicle', fn ($vq) => $vq->where('license_plate', 'like', '%' . $this->search . '%'));
                });
            })
            ->latest()
            ->paginate(15);
    }

    public function getStatusOptions(): array
    {
        return [
            '' => 'All Statuses',
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getTypeOptions(): array
    {
        return [
            '' => 'All Types',
            'glass_sale' => 'Glass Sale',
            'glass_installation' => 'Glass Installation',
            'service_only' => 'Service Only',
        ];
    }

    public function render()
    {
        return view('livewire.transaction-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Transactions',
            'header' => 'Transactions',
        ]);
    }
}