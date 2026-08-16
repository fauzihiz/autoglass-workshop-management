<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class CustomerShow extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer->load([
            'vehicles.brand',
            'vehicles.model',
            'transactions' => fn ($q) => $q->latest(),
        ]);
    }

    public function render()
    {
        return view('livewire.customer-show')->layout('layouts.app', [
            'title' => $this->customer->name,
            'header' => 'Customer Details',
        ]);
    }
}