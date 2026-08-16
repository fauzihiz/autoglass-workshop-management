<?php

namespace App\Livewire;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\GlassProduct;
use App\Models\Transaction;
use Livewire\Component;

class ComplaintLookup extends Component
{
    public string $licensePlate = '';
    public string $customerName = '';
    public ?int $brandId = null;
    public ?int $modelId = null;
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $glassProductId = null;

    /** @var \Illuminate\Support\Collection|null */
    public $results = null;

    public function search(): void
    {
        $this->results = Transaction::query()
            ->with(['customer', 'vehicle.brand', 'vehicle.model', 'items.itemable'])
            ->when($this->licensePlate !== '', function ($q) {
                $q->whereHas('vehicle', fn ($vq) => $vq->where('license_plate', 'like', '%' . $this->licensePlate . '%'));
            })
            ->when($this->customerName !== '', function ($q) {
                $q->whereHas('customer', fn ($cq) => $cq->where('name', 'like', '%' . $this->customerName . '%'));
            })
            ->when($this->brandId, function ($q) {
                $q->whereHas('vehicle', fn ($vq) => $vq->where('car_brand_id', $this->brandId));
            })
            ->when($this->modelId, function ($q) {
                $q->whereHas('vehicle', fn ($vq) => $vq->where('car_model_id', $this->modelId));
            })
            ->when($this->dateFrom !== '', function ($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo !== '', function ($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->glassProductId, function ($q) {
                $q->whereHas('items', function ($iq) {
                    $iq->where('itemable_type', GlassProduct::class)
                        ->where('itemable_id', $this->glassProductId);
                });
            })
            ->latest()
            ->get();
    }

    public function getBrandOptions(): array
    {
        return CarBrand::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getModelOptions(): array
    {
        return CarModel::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getProductOptions(): array
    {
        return GlassProduct::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function clear(): void
    {
        $this->licensePlate = '';
        $this->customerName = '';
        $this->brandId = null;
        $this->modelId = null;
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->glassProductId = null;
        $this->results = null;
    }

    public function render()
    {
        return view('livewire.complaint-lookup')->layout('layouts.app', [
            'title' => 'Complaint Lookup',
            'header' => 'Complaint Lookup',
        ]);
    }
}