<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Customer;
use App\Models\Vehicle;

class VehicleIndex extends \Livewire\Component
{
    use HasCrudOperations;

    public int $customerId = 0;
    public int $carBrandId = 0;
    public int $carModelId = 0;
    public string $licensePlate = '';
    public string $year = '';
    public string $color = '';
    public string $notes = '';

    public array $filteredModels = [];

    public function getModelClass(): string
    {
        return Vehicle::class;
    }

    public function getTitle(): string
    {
        return 'Vehicle';
    }

    public function getSearchFields(): array
    {
        return ['license_plate', 'color', 'year'];
    }

    public function resetForm(): void
    {
        $this->customerId = 0;
        $this->carBrandId = 0;
        $this->carModelId = 0;
        $this->licensePlate = '';
        $this->year = '';
        $this->color = '';
        $this->notes = '';
        $this->filteredModels = [];
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->customerId = $record->customer_id;
        $this->carBrandId = $record->car_brand_id;
        $this->carModelId = $record->car_model_id;
        $this->licensePlate = $record->license_plate;
        $this->year = $record->year ?? '';
        $this->color = $record->color ?? '';
        $this->notes = $record->notes ?? '';
        $this->filteredModels = $this->getModelsForBrand($this->carBrandId);
    }

    public function updatedCarBrandId(): void
    {
        $this->carModelId = 0;
        $this->filteredModels = $this->getModelsForBrand($this->carBrandId);
    }

    public function getModelsForBrand(int $brandId): array
    {
        if ($brandId <= 0) {
            return [];
        }

        return CarModel::where('car_brand_id', $brandId)->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'customerId' => 'required|exists:customers,id',
            'carBrandId' => 'required|exists:car_brands,id',
            'carModelId' => 'required|exists:car_models,id',
            'licensePlate' => 'required|string|max:255|unique:vehicles,license_plate' . ($this->editingId ? ',' . $this->editingId : ''),
            'year' => 'nullable|string|max:4',
            'color' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);

        $data = [
            'customer_id' => $validated['customerId'],
            'car_brand_id' => $validated['carBrandId'],
            'car_model_id' => $validated['carModelId'],
            'license_plate' => $validated['licensePlate'],
            'year' => $validated['year'] ?? null,
            'color' => $validated['color'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($this->editingId) {
            Vehicle::findOrFail($this->editingId)->update($data);
        } else {
            Vehicle::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function getCustomerOptions(): array
    {
        return Customer::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getBrandOptions(): array
    {
        return CarBrand::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.vehicle-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Vehicles',
            'header' => 'Vehicles',
        ]);
    }
}