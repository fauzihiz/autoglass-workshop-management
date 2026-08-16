<?php

namespace App\Livewire;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Customer;
use App\Models\GlassProduct;
use App\Models\Service;
use App\Models\ServiceAssignment;
use App\Models\StockLot;
use App\Models\Technician;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Vehicle;
use Livewire\Component;

class TransactionCreate extends Component
{
    public int $currentStep = 1;

    // Step 1: Type
    public ?string $type = null;

    // Step 2: Customer & Vehicle
    public ?int $customerId = null;
    public ?int $vehicleId = null;
    public string $customerSearch = '';
    public bool $showNewCustomerForm = false;
    public string $newCustomerName = '';
    public string $newCustomerPhone = '';
    public string $newCustomerEmail = '';
    public string $newCustomerAddress = '';
    public bool $showNewVehicleForm = false;
    public string $newVehiclePlate = '';
    public ?int $newVehicleBrandId = null;
    public ?int $newVehicleModelId = null;
    public string $newVehicleYear = '';
    public string $newVehicleColor = '';

    // Step 3: Items
    public string $itemSearch = '';
    public ?int $selectedProductId = null;
    public int $glassQuantity = 1;
    public float $glassUnitPrice = 0;
    public ?int $selectedStockLotId = null;
    public ?int $selectedServiceId = null;
    public int $serviceQuantity = 1;
    public float $serviceUnitPrice = 0;
    public ?int $selectedTechnicianId = null;

    /** @var array<int, array> */
    public array $items = [];

    // Step 4: Notes
    public string $notes = '';

    public function mount(): void
    {
        $this->type = request()->query('type');
        if ($this->type && in_array($this->type, array_column(TransactionType::cases(), 'value'))) {
            $this->currentStep = 2;
        }
    }

    // ── Navigation ──

    public function nextStep(): void
    {
        if ($this->currentStep === 1 && !$this->type) {
            $this->addError('type', 'Please select a transaction type.');
            return;
        }

        if ($this->currentStep === 2) {
            $this->validate([
                'customerId' => 'required|exists:customers,id',
                'vehicleId' => 'required|exists:vehicles,id',
            ]);
        }

        if ($this->currentStep === 3 && empty($this->items)) {
            $this->addError('items', 'Please add at least one item.');
            return;
        }

        $this->clearErrors();
        $this->currentStep = min($this->currentStep + 1, 4);
    }

    public function prevStep(): void
    {
        $this->clearErrors();
        $this->currentStep = max($this->currentStep - 1, 1);
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->currentStep) {
            $this->clearErrors();
            $this->currentStep = $step;
        }
    }

    // ── Step 2: Customer & Vehicle ──

    public function getCustomerOptions(): array
    {
        $query = Customer::query();
        if ($this->customerSearch !== '') {
            $query->where('name', 'like', '%' . $this->customerSearch . '%');
        }
        return $query->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getVehicleOptions(): array
    {
        if (!$this->customerId) return [];
        return Vehicle::where('customer_id', $this->customerId)
            ->with('brand', 'model')
            ->get()
            ->mapWithKeys(fn ($v) => [$v->id => $v->license_plate . ' — ' . ($v->brand->name ?? '') . ' ' . ($v->model->name ?? '')])
            ->toArray();
    }

    public function getBrandOptions(): array
    {
        return CarBrand::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getModelOptions(): array
    {
        if (!$this->newVehicleBrandId) return [];
        return CarModel::where('car_brand_id', $this->newVehicleBrandId)
            ->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function saveNewCustomer(): void
    {
        $validated = $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerPhone' => 'nullable|string|max:255',
            'newCustomerEmail' => 'nullable|email|max:255',
            'newCustomerAddress' => 'nullable|string|max:255',
        ]);

        $customer = Customer::create([
            'name' => $validated['newCustomerName'],
            'phone' => $validated['newCustomerPhone'] ?: null,
            'email' => $validated['newCustomerEmail'] ?: null,
            'address' => $validated['newCustomerAddress'] ?: null,
        ]);

        $this->customerId = $customer->id;
        $this->showNewCustomerForm = false;
        $this->reset(['newCustomerName', 'newCustomerPhone', 'newCustomerEmail', 'newCustomerAddress']);
    }

    public function saveNewVehicle(): void
    {
        $validated = $this->validate([
            'newVehiclePlate' => 'required|string|max:255',
            'newVehicleBrandId' => 'required|exists:car_brands,id',
            'newVehicleModelId' => 'required|exists:car_models,id',
            'newVehicleYear' => 'nullable|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'newVehicleColor' => 'nullable|string|max:255',
        ]);

        $vehicle = Vehicle::create([
            'customer_id' => $this->customerId,
            'car_brand_id' => $validated['newVehicleBrandId'],
            'car_model_id' => $validated['newVehicleModelId'],
            'license_plate' => $validated['newVehiclePlate'],
            'year' => $validated['newVehicleYear'] ?: null,
            'color' => $validated['newVehicleColor'] ?: null,
        ]);

        $this->vehicleId = $vehicle->id;
        $this->showNewVehicleForm = false;
        $this->reset(['newVehiclePlate', 'newVehicleBrandId', 'newVehicleModelId', 'newVehicleYear', 'newVehicleColor']);
    }

    // ── Step 3: Items ──

    public function getGlassProductOptions(): array
    {
        $query = GlassProduct::where('is_active', true);
        if ($this->itemSearch !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->itemSearch . '%')
                    ->orWhere('sku', 'like', '%' . $this->itemSearch . '%');
            });
        }
        return $query->orderBy('name')
            ->get()
            ->filter(fn ($p) => $p->total_stock > 0)
            ->mapWithKeys(fn ($p) => [$p->id => $p->sku . ' — ' . $p->name . ' (stock: ' . $p->total_stock . ')'])
            ->toArray();
    }

    public function updatedSelectedProductId(): void
    {
        $this->selectedStockLotId = null;
        $this->glassQuantity = 1;
        $this->glassUnitPrice = 0;
    }

    public function getStockLotOptions(): array
    {
        if (!$this->selectedProductId) return [];
        return StockLot::where('glass_product_id', $this->selectedProductId)
            ->with('balances')
            ->get()
            ->filter(fn ($lot) => $lot->total_quantity > 0)
            ->mapWithKeys(fn ($lot) => [
                $lot->id => $lot->lot_number . ' — Rp ' . number_format($lot->purchase_cost, 0, ',', '.') . ' (qty: ' . $lot->total_quantity . ')',
            ])
            ->toArray();
    }

    public function getLotStockDetail(): ?array
    {
        if (!$this->selectedStockLotId) return null;
        $lot = StockLot::with('balances.rack')->find($this->selectedStockLotId);
        if (!$lot) return null;
        return [
            'lot_number' => $lot->lot_number,
            'purchase_cost' => $lot->purchase_cost,
            'total_quantity' => $lot->total_quantity,
            'balances' => $lot->balances->map(fn ($b) => [
                'rack' => $b->rack->name ?? '—',
                'quantity' => $b->quantity,
            ])->toArray(),
            'max_quantity' => $lot->total_quantity,
        ];
    }

    public function updatedSelectedStockLotId(): void
    {
        $detail = $this->getLotStockDetail();
        if ($detail) {
            $this->glassUnitPrice = (float) $detail['purchase_cost'] * 1.5;
        }
    }

    public function addGlassItem(): void
    {
        $validated = $this->validate([
            'selectedProductId' => 'required|exists:glass_products,id',
            'selectedStockLotId' => 'required|exists:stock_lots,id',
            'glassQuantity' => 'required|integer|min:1',
            'glassUnitPrice' => 'required|numeric|min:0',
        ]);

        $product = GlassProduct::find($validated['selectedProductId']);
        $lot = StockLot::find($validated['selectedStockLotId']);

        $this->items[] = [
            'type' => 'glass',
            'id' => $product->id,
            'name' => $product->sku . ' — ' . $product->name,
            'quantity' => $validated['glassQuantity'],
            'unit_price' => (float) $validated['glassUnitPrice'],
            'total_price' => (float) $validated['glassQuantity'] * (float) $validated['glassUnitPrice'],
            'notes' => '',
            'lot_id' => $lot->id,
            'lot_number' => $lot->lot_number,
            'purchase_cost' => (float) $lot->purchase_cost,
        ];

        $this->selectedProductId = null;
        $this->selectedStockLotId = null;
        $this->glassQuantity = 1;
        $this->glassUnitPrice = 0;
        $this->itemSearch = '';
        $this->clearErrors();
    }

    public function getServiceOptions(): array
    {
        $query = Service::where('is_active', true);
        if ($this->itemSearch !== '') {
            $query->where('name', 'like', '%' . $this->itemSearch . '%');
        }
        return $query->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getTechnicianOptions(): array
    {
        return Technician::where('is_active', true)
            ->orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function updatedSelectedServiceId(): void
    {
        if ($this->selectedServiceId) {
            $service = Service::find($this->selectedServiceId);
            $this->serviceUnitPrice = $service ? (float) $service->default_price : 0;
        }
    }

    public function addServiceItem(): void
    {
        $validated = $this->validate([
            'selectedServiceId' => 'required|exists:services,id',
            'serviceQuantity' => 'required|integer|min:1',
            'serviceUnitPrice' => 'required|numeric|min:0',
            'selectedTechnicianId' => 'nullable|exists:technicians,id',
        ]);

        $service = Service::find($validated['selectedServiceId']);
        $technician = $validated['selectedTechnicianId'] ? Technician::find($validated['selectedTechnicianId']) : null;

        $this->items[] = [
            'type' => 'service',
            'id' => $service->id,
            'name' => $service->name,
            'quantity' => $validated['serviceQuantity'],
            'unit_price' => (float) $validated['serviceUnitPrice'],
            'total_price' => (float) $validated['serviceQuantity'] * (float) $validated['serviceUnitPrice'],
            'notes' => '',
            'technician_id' => $technician?->id,
            'technician_name' => $technician?->name,
        ];

        $this->selectedServiceId = null;
        $this->selectedTechnicianId = null;
        $this->serviceQuantity = 1;
        $this->serviceUnitPrice = 0;
        $this->itemSearch = '';
        $this->clearErrors();
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    // ── Computed ──

    public function getTotalAttribute(): float
    {
        return array_sum(array_column($this->items, 'total_price'));
    }

    public function getEstimatedProfitAttribute(): float
    {
        $glassCost = 0;
        foreach ($this->items as $item) {
            if ($item['type'] === 'glass' && isset($item['purchase_cost'])) {
                $glassCost += $item['purchase_cost'] * $item['quantity'];
            }
        }
        return $this->total - $glassCost;
    }

    // ── Step 4: Confirm ──

    public function createTransaction()
    {
        if (empty($this->items)) {
            $this->addError('items', 'Please add at least one item.');
            return;
        }

        $transaction = Transaction::create([
            'customer_id' => $this->customerId,
            'vehicle_id' => $this->vehicleId,
            'type' => $this->type,
            'invoice_number' => Transaction::generateInvoiceNumber(),
            'status' => TransactionStatus::Pending->value,
            'notes' => $this->notes ?: null,
        ]);

        foreach ($this->items as $itemData) {
            $itemableType = $itemData['type'] === 'glass' ? GlassProduct::class : Service::class;

            $txItem = TransactionItem::create([
                'transaction_id' => $transaction->id,
                'itemable_type' => $itemableType,
                'itemable_id' => $itemData['id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'total_price' => $itemData['total_price'],
                'notes' => $itemData['notes'] ?: null,
            ]);

            if ($itemData['type'] === 'service' && !empty($itemData['technician_id'])) {
                ServiceAssignment::create([
                    'transaction_item_id' => $txItem->id,
                    'technician_id' => $itemData['technician_id'],
                ]);
            }
        }

        return redirect()->route('transactions.show', $transaction);
    }

    public function getTypeLabel(): string
    {
        return TransactionType::tryFrom($this->type)?->label() ?? '';
    }

    public function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.transaction-create', [
            'total' => $this->total,
            'estimatedProfit' => $this->estimated_profit,
        ])->layout('layouts.app', [
            'title' => 'New Transaction',
            'header' => 'New Transaction',
        ]);
    }
}