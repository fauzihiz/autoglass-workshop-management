<div>
    <x-page-header :title="$transaction->invoice_number" subtitle="Transaction details">
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.print', $transaction) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" /></svg>
                Print Invoice
            </a>
            @if ($transaction->status === 'pending')
                <x-button variant="primary" class="bg-green-600 hover:bg-green-500" wire:click="confirmTransaction" onclick="return confirm('Confirm this transaction? Stock will be deducted.')">✓ Confirm</x-button>
                <x-button variant="danger" wire:click="cancelTransaction" onclick="return confirm('Cancel this transaction?')">Cancel</x-button>
            @endif
            @if ($transaction->status === 'confirmed')
                <x-button variant="danger" wire:click="cancelTransaction" onclick="return confirm('Cancel this confirmed transaction? Stock will be restored.')">Cancel & Restore Stock</x-button>
            @endif
            @if ($transaction->balance_due > 0 && $transaction->status === 'confirmed')
                <x-button variant="primary" wire:click="openPaymentModal">Record Payment</x-button>
            @endif
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left Column: Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-{{ $transaction->status_color }}-200 bg-{{ $transaction->status_color }}-50 p-4">
                <div class="flex items-center gap-3">
                    <x-badge :variant="$transaction->status_color" class="text-sm">{{ ucfirst($transaction->status) }}</x-badge>
                    <span class="text-sm font-medium text-{{ $transaction->status_color }}-700">
                        {{ $transaction->type_label }} — {{ $transaction->created_at->format('d M Y H:i') }}
                    </span>
                </div>
            </div>

            {{-- Customer & Vehicle --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Customer & Vehicle</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                    <div><span class="text-gray-500">Customer:</span> <span class="ml-1 font-medium text-gray-900">@if ($transaction->customer)<a href="{{ route('customers.show', $transaction->customer) }}" class="text-blue-600 hover:underline">{{ $transaction->customer->name }}</a>@else —@endif</span></div>
                    <div><span class="text-gray-500">Phone:</span> <span class="ml-1 font-medium text-gray-900">{{ $transaction->customer->phone ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Vehicle:</span> <span class="ml-1 font-medium text-gray-900">@if ($transaction->vehicle)<a href="{{ route('vehicles.show', $transaction->vehicle) }}" class="text-blue-600 hover:underline">{{ $transaction->vehicle->license_plate }}</a>@else —@endif</span></div>
                    <div><span class="text-gray-500">Model:</span> <span class="ml-1 font-medium text-gray-900">{{ ($transaction->vehicle->brand->name ?? '') . ' ' . ($transaction->vehicle->model->name ?? '') }}</span></div>
                </div>

            {{-- Items --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4"><h3 class="font-semibold text-gray-900">Items</h3></div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Unit Price</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($transaction->items as $idx => $item)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $idx + 1 }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->itemable->name ?? '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <x-badge :variant="str_contains($item->itemable_type, 'Glass') ? 'blue' : 'amber'">
                                            {{ str_contains($item->itemable_type, 'Glass') ? 'Glass' : 'Service' }}
                                        </x-badge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900">{{ $item->quantity }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900">{{ 'Rp ' . number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ 'Rp ' . number_format($item->total_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Totals & Payments --}}
        <div class="space-y-6">
            {{-- Totals --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 font-semibold text-gray-900">Summary</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">{{ 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Glass Cost</span><span class="font-medium text-gray-900">{{ 'Rp ' . number_format($transaction->glass_cost, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Profit</span><span class="font-semibold text-green-600">{{ 'Rp ' . number_format($transaction->profit, 0, ',', '.') }}</span></div>
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between"><span class="text-gray-500">Paid</span><span class="font-medium text-green-600">{{ 'Rp ' . number_format($transaction->total_paid, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Balance Due</span><span class="font-semibold text-{{ $transaction->balance_due > 0 ? 'red' : 'green' }}-600">{{ 'Rp ' . number_format($transaction->balance_due, 0, ',', '.') }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Payments --}}
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4"><h3 class="font-semibold text-gray-900">Payments</h3></div>
                @if ($transaction->payments->isEmpty())
                    <div class="px-6 py-8 text-center text-sm text-gray-500">No payments recorded yet.</div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($transaction->payments as $payment)
                            <div class="px-6 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ 'Rp ' . number_format($payment->amount, 0, ',', '.') }}</span>
                                        <x-badge variant="green" class="ml-2">{{ ucfirst($payment->method) }}</x-badge>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $payment->paid_at?->format('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($transaction->notes)
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-2 font-semibold text-gray-900">Notes</h3>
                    <p class="text-sm text-gray-600">{{ $transaction->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Payment Modal --}}
    @if ($showPaymentModal)
        <x-modal title="Record Payment" wire:close="closePaymentModal">
            <form wire:submit.prevent="savePayment" class="space-y-4">
                <x-input label="Amount" wire:model="paymentAmount" name="paymentAmount" type="number" step="1000" min="0" max="{{ $transaction->balance_due }}" required :error="$errors->first('paymentAmount')" />
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Payment Method</label>
                    <div class="flex flex-wrap gap-3">
                        @foreach (['cash' => 'Cash', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'other' => 'Other'] as $val => $label)
                            <label class="flex items-center gap-2">
                                <input type="radio" wire:model="paymentMethod" value="{{ $val }}" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('paymentMethod') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <x-input label="Reference Number (optional)" wire:model="paymentReference" name="paymentReference" placeholder="Transfer ref, receipt no." :error="$errors->first('paymentReference')" />
                <x-input label="Notes (optional)" wire:model="paymentNotes" name="paymentNotes" placeholder="Payment notes" :error="$errors->first('paymentNotes')" />
                <div class="flex justify-end gap-3 pt-4">
                    <x-button wire:click="closePaymentModal" type="button" variant="secondary">Cancel</x-button>
                    <x-button type="submit" variant="primary">Save Payment</x-button>
                </div>
            </form>
        </x-modal>
    @endif
</div>
            </div>