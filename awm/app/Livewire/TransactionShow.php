<?php

namespace App\Livewire;

use App\Enums\PaymentMethod;
use App\Enums\TransactionStatus;
use App\Models\Payment;
use App\Models\StockAllocation;
use App\Models\StockBalance;
use App\Models\StockLot;
use App\Models\StockMovement;
use App\Enums\StockMovementType;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TransactionShow extends Component
{
    public Transaction $transaction;

    // Payment modal
    public bool $showPaymentModal = false;
    public float $paymentAmount = 0;
    public string $paymentMethod = 'cash';
    public string $paymentReference = '';
    public string $paymentNotes = '';

    public function mount(Transaction $transaction): void
    {
        $this->transaction = $transaction->load([
            'customer',
            'vehicle.brand',
            'vehicle.model',
            'items.itemable',
            'payments',
            'items.allocations.lot',
            'items.allocations.rack',
            'items.serviceAssignment.technician',
        ]);
    }

    // ── Actions ──

    public function confirmTransaction(): void
    {
        if ($this->transaction->status !== TransactionStatus::Pending->value) return;

        DB::transaction(function () {
            $glassItems = $this->transaction->items
                ->where('itemable_type', \App\Models\GlassProduct::class);

            foreach ($glassItems as $item) {
                // Validate stock availability
                $totalAvailable = StockBalance::where('quantity', '>', 0)
                    ->whereHas('lot', fn ($q) => $q->where('glass_product_id', $item->itemable_id))
                    ->sum('quantity');

                if ($totalAvailable < $item->quantity) {
                    $productName = $item->itemable->name ?? 'Unknown Product';
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'stock' => "Insufficient stock for {$productName}. Available: {$totalAvailable}, Requested: {$item->quantity}.",
                    ]);
                }

                $remaining = $item->quantity;
                $lots = StockBalance::where('quantity', '>', 0)
                    ->whereHas('lot', fn ($q) => $q->where('glass_product_id', $item->itemable_id))
                    ->with('lot')
                    ->orderBy('lot.purchase_date')
                    ->get();

                foreach ($lots as $balance) {
                    if ($remaining <= 0) break;
                    $deduct = min($remaining, $balance->quantity);
                    $balance->decrement('quantity', $deduct);

                    StockAllocation::create([
                        'transaction_item_id' => $item->id,
                        'stock_lot_id' => $balance->stock_lot_id,
                        'rack_id' => $balance->rack_id,
                        'quantity' => $deduct,
                    ]);

                    StockMovement::create([
                        'stock_lot_id' => $balance->stock_lot_id,
                        'rack_id' => $balance->rack_id,
                        'type' => StockMovementType::Out->value,
                        'quantity' => $deduct,
                        'reference_type' => Transaction::class,
                        'reference_id' => $this->transaction->id,
                        'notes' => 'Stock out for ' . $this->transaction->invoice_number,
                    ]);

                    $remaining -= $deduct;
                }
            }

            $this->transaction->update(['status' => TransactionStatus::Confirmed->value]);
        });

        $this->transaction->load(['items.allocations.lot', 'items.allocations.rack']);
    }

    public function cancelTransaction(): void
    {
        $currentStatus = $this->transaction->status;

        if (! in_array($currentStatus, [
            TransactionStatus::Pending->value,
            TransactionStatus::Confirmed->value,
        ])) {
            return;
        }

        DB::transaction(function () use ($currentStatus) {
            // If the transaction was confirmed, restore stock from allocations
            if ($currentStatus === TransactionStatus::Confirmed->value) {
                $allocations = StockAllocation::whereHas('transactionItem', function ($q) {
                    $q->where('transaction_id', $this->transaction->id);
                })->get();

                foreach ($allocations as $allocation) {
                    // Restore stock balance
                    $balance = StockBalance::firstOrCreate(
                        ['stock_lot_id' => $allocation->stock_lot_id, 'rack_id' => $allocation->rack_id],
                        ['quantity' => 0]
                    );
                    $balance->increment('quantity', $allocation->quantity);

                    // Create reverse movement record
                    StockMovement::create([
                        'stock_lot_id' => $allocation->stock_lot_id,
                        'rack_id' => $allocation->rack_id,
                        'type' => StockMovementType::In->value,
                        'quantity' => $allocation->quantity,
                        'reference_type' => Transaction::class,
                        'reference_id' => $this->transaction->id,
                        'notes' => 'Restored from cancelled ' . $this->transaction->invoice_number,
                    ]);
                }

                // Remove allocation records
                StockAllocation::whereHas('transactionItem', function ($q) {
                    $q->where('transaction_id', $this->transaction->id);
                })->delete();
            }

            $this->transaction->update(['status' => TransactionStatus::Cancelled->value]);
        });
    }

    // ── Payments ──

    public function openPaymentModal(): void
    {
        $this->paymentAmount = $this->transaction->balance_due;
        $this->paymentMethod = 'cash';
        $this->paymentReference = '';
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->resetValidation();
    }

    public function savePayment(): void
    {
        $validated = $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01|max:' . $this->transaction->balance_due,
            'paymentMethod' => 'required|in:cash,transfer,qris,other',
            'paymentReference' => 'nullable|string|max:255',
            'paymentNotes' => 'nullable|string|max:255',
        ]);

        Payment::create([
            'transaction_id' => $this->transaction->id,
            'amount' => $validated['paymentAmount'],
            'method' => $validated['paymentMethod'],
            'reference_number' => $validated['paymentReference'] ?: null,
            'notes' => $validated['paymentNotes'] ?: null,
            'paid_at' => now(),
        ]);

        $this->closePaymentModal();
        $this->transaction->refresh()->load(['payments']);
    }

    // ── Helpers ──

    public function getPaymentMethodOptions(): array
    {
        return array_map(fn ($m) => $m->label(), PaymentMethod::cases());
    }

    public function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.transaction-show', [
            'transaction' => $this->transaction,
        ])->layout('layouts.app', [
            'title' => 'Transaction ' . $this->transaction->invoice_number,
            'header' => 'Transaction Detail',
        ]);
    }
}