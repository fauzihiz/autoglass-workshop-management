<?php

namespace App\Livewire;

use App\Models\Technician;
use Livewire\Component;

class TechnicianShow extends Component
{
    public Technician $technician;

    public function mount(Technician $technician): void
    {
        $this->technician = $technician->load([
            'assignments.transactionItem.itemable',
            'assignments.transactionItem.transaction.customer',
            'assignments.transactionItem.transaction.vehicle',
        ]);
    }

    public function getAssignments()
    {
        return $this->technician->assignments
            ->sortByDesc('transactionItem.transaction.created_at')
            ->values();
    }

    public function render()
    {
        return view('livewire.technician-show', [
            'assignments' => $this->getAssignments(),
        ])->layout('layouts.app', [
            'title' => $this->technician->name,
            'header' => 'Technician Details',
        ]);
    }
}