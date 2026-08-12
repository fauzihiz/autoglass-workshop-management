<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasCrudOperations;
use App\Models\Accessory;
use App\Models\GlassPosition;
use App\Models\GlassProduct;
use App\Models\ProductCompatibility;
use Livewire\Component;

class GlassProductIndex extends Component
{
    use HasCrudOperations;

    public int $glassPositionId = 0;
    public string $name = '';
    public string $sku = '';
    public string $description = '';
    public int $minimumStock = 0;
    public bool $isActive = true;

    public bool $showCompatibilityModal = false;
    public int $compatibilityProductId = 0;
    public int $compatCarModelId = 0;
    public string $compatYearFrom = '';
    public string $compatYearTo = '';
    public ?int $editingCompatibilityId = null;

    public bool $showAccessoryModal = false;
    public int $accessoryProductId = 0;
    public int $selectedAccessoryId = 0;

    public function getModelClass(): string
    {
        return GlassProduct::class;
    }

    public function getTitle(): string
    {
        return 'Glass Product';
    }

    public function getSearchFields(): array
    {
        return ['name', 'sku', 'description'];
    }

    public function resetForm(): void
    {
        $this->glassPositionId = 0;
        $this->name = '';
        $this->sku = '';
        $this->description = '';
        $this->minimumStock = 0;
        $this->isActive = true;
    }

    public function fillFromModel(\Illuminate\Database\Eloquent\Model $record): void
    {
        $this->glassPositionId = $record->glass_position_id;
        $this->name = $record->name;
        $this->sku = $record->sku;
        $this->description = $record->description ?? '';
        $this->minimumStock = (int) $record->minimum_stock;
        $this->isActive = $record->is_active;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'glassPositionId' => 'required|exists:glass_positions,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:glass_products,sku'
                . ($this->editingId ? ',' . $this->editingId : ''),
            'description' => 'nullable|string|max:255',
            'minimumStock' => 'required|integer|min:0',
            'isActive' => 'boolean',
        ]);

        $data = [
            'glass_position_id' => $validated['glassPositionId'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'description' => $validated['description'] ?? null,
            'minimum_stock' => $validated['minimumStock'],
            'is_active' => $validated['isActive'],
        ];

        if ($this->editingId) {
            GlassProduct::findOrFail($this->editingId)->update($data);
        } else {
            GlassProduct::create($data);
        }

        $this->showModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->dispatch('saved');
    }

    public function getPositionOptions(): array
    {
        return GlassPosition::orderBy('name')->pluck('name', 'id')->toArray();
    }

    // --- Compatibility Management ---

    public function openCompatibilityModal(int $productId): void
    {
        $this->compatibilityProductId = $productId;
        $this->compatCarModelId = 0;
        $this->compatYearFrom = '';
        $this->compatYearTo = '';
        $this->editingCompatibilityId = null;
        $this->showCompatibilityModal = true;
    }

    public function editCompatibility(ProductCompatibility $record): void
    {
        $this->compatibilityProductId = $record->glass_product_id;
        $this->compatCarModelId = $record->car_model_id;
        $this->compatYearFrom = $record->year_from ? (string) $record->year_from : '';
        $this->compatYearTo = $record->year_to ? (string) $record->year_to : '';
        $this->editingCompatibilityId = $record->id;
        $this->showCompatibilityModal = true;
    }

    public function saveCompatibility(): void
    {
        $validated = $this->validate([
            'compatCarModelId' => 'required|exists:car_models,id',
            'compatYearFrom' => 'nullable|string|max:4',
            'compatYearTo' => 'nullable|string|max:4',
        ]);

        $data = [
            'glass_product_id' => $this->compatibilityProductId,
            'car_model_id' => $validated['compatCarModelId'],
            'year_from' => $validated['compatYearFrom'] ?: null,
            'year_to' => $validated['compatYearTo'] ?: null,
        ];

        if ($this->editingCompatibilityId) {
            ProductCompatibility::findOrFail($this->editingCompatibilityId)->update($data);
        } else {
            ProductCompatibility::create($data);
        }

        $this->showCompatibilityModal = false;
        $this->editingCompatibilityId = null;
        $this->dispatch('saved');
    }

    public function deleteCompatibility(ProductCompatibility $record): void
    {
        $record->delete();
        $this->dispatch('saved');
    }

    // --- Accessory Management ---

    public function openAccessoryModal(int $productId): void
    {
        $this->accessoryProductId = $productId;
        $this->selectedAccessoryId = 0;
        $this->showAccessoryModal = true;
    }

    public function saveAccessory(): void
    {
        $validated = $this->validate([
            'selectedAccessoryId' => 'required|exists:accessories,id',
        ]);

        $product = GlassProduct::findOrFail($this->accessoryProductId);
        $existing = $product->accessories()
            ->where('accessory_id', $validated['selectedAccessoryId'])
            ->first();

        if (! $existing) {
            $product->accessories()->attach($validated['selectedAccessoryId']);
        }

        $this->showAccessoryModal = false;
        $this->selectedAccessoryId = 0;
        $this->dispatch('saved');
    }

    public function removeAccessory(GlassProduct $product, Accessory $accessory): void
    {
        $product->accessories()->detach($accessory->id);
        $this->dispatch('saved');
    }

    public function getAccessoryOptions(): array
    {
        return Accessory::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.glass-product-index', [
            'items' => $this->getItems(),
        ])->layout('layouts.app', [
            'title' => 'Glass Products',
            'header' => 'Glass Products',
        ]);
    }
}