<?php

namespace App\Livewire;

use App\Models\Vehicle;
use Livewire\Component;

class VehicleShow extends Component
{
    public Vehicle $vehicle;

    public function mount(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle->load([
            'customer',
            'brand',
            'model',
            'transactions' => fn ($q) => $q->latest(),
        ]);
    }

    public function render()
    {
        return view('livewire.vehicle-show')->layout('layouts.app', [
            'title' => $this->vehicle->license_plate,
            'header' => 'Vehicle Details',
        ]);
    }
}