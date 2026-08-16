<div>
    {{-- Progress Steps --}}
    <div class="mb-8">
        <nav aria-label="Progress">
            <ol class="flex items-center">
                @php $steps = [1 => 'Type', 2 => 'Customer & Vehicle', 3 => 'Items', 4 => 'Review']; @endphp
                @foreach ($steps as $num => $label)
                    <li class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                        <button wire:click="goToStep({{ $num }})" type="button" class="flex items-center gap-2 {{ $num <= $currentStep ? 'text-blue-600' : 'text-gray-400' }}" @if ($num > $currentStep) disabled @endif>
                            <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 {{ $num < $currentStep ? 'border-blue-600 bg-blue-600 text-white' : ($num === $currentStep ? 'border-blue-600 bg-white text-blue-600' : 'border-gray-300 bg-white text-gray-400') }} text-sm font-semibold">
                                @if ($num < $currentStep)
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                @else
                                    {{ $num }}
                                @endif
                            </span>
                            <span class="text-sm font-medium hidden sm:inline">{{ $label }}</span>
                        </button>
                        @if (!$loop->last)
                            <div class="mx-3 h-0.5 flex-1 {{ $num < $currentStep ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>

    {{-- Step 1: Type Selection --}}
    @if ($currentStep === 1)
        <div class="mx-auto max-w-3xl">
            <h2 class="mb-2 text-xl font-bold text-gray-900">Select Transaction Type</h2>
            <p class="mb-6 text-sm text-gray-500">Choose the type of transaction to create.</p>
            @error('type') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <button wire:click="selectType('glass_sale')" class="group rounded-xl border-2 border-gray-200 bg-white p-6 text-left shadow-sm transition hover:border-blue-500 hover:shadow-md">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75-2.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Glass Sale</h3>
                    <p class="mt-1 text-sm text-gray-500">Customer purchases glass without installation</p>
                </button>
                <button wire:click="selectType('glass_installation')" class="group rounded-xl border-2 border-gray-200 bg-white p-6 text-left shadow-sm transition hover:border-blue-500 hover:shadow-md">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600 group-hover:bg-green-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Glass Installation</h3>
                    <p class="mt-1 text-sm text-gray-500">Glass purchase with installation service</p>
                </button>
                <button wire:click="selectType('service_only')" class="group rounded-xl border-2 border-gray-200 bg-white p-6 text-left shadow-sm transition hover:border-blue-500 hover:shadow-md">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600 group-hover:bg-amber-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.545 3.42-3.586-3.586a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 20.25 18V9.25a2.25 2.25 0 0 0-2.25-2.25h-3.391a1.5 1.5 0 0 1-1.06-.44Z" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Service Only</h3>
                    <p class="mt-1 text-sm text-gray-500">Service without glass purchase</p>
                </button>
            </div>
        </div>

    {{-- Step 2: Customer & Vehicle --}}
    @if ($currentStep === 2)
        <div class="mx-auto max-w-3xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Customer & Vehicle</h2>
                    <p class="mt-1 text-sm text-gray-500">Select the customer and vehicle for this {{ $this->getTypeLabel() }} transaction.</p>
                </div>
                <button wire:click="prevStep" type="button" class="text-sm font-medium text-gray-500 hover:text-gray-700">← Back</button>
            </div>

            {{-- Customer --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Customer</h3>
                    <button wire:click="$set('showNewCustomerForm', {{ $showNewCustomerForm ? 'false' : 'true' }})" type="button" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        {{ $showNewCustomerForm ? '← Select Existing' : '+ New Customer' }}
                    </button>
                </div>
                @if ($showNewCustomerForm)
                    <div class="space-y-3">
                        <x-input label="Customer Name" wire:model="newCustomerName" name="newCustomerName" placeholder="Full name" required :error="$errors->first('newCustomerName')" />
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <x-input label="Phone" wire:model="newCustomerPhone" name="newCustomerPhone" placeholder="08123456789" :error="$errors->first('newCustomerPhone')" />
                            <x-input label="Email" wire:model="newCustomerEmail" name="newCustomerEmail" type="email" placeholder="email@example.com" :error="$errors->first('newCustomerEmail')" />
                        </div>
                        <x-input label="Address" wire:model="newCustomerAddress" name="newCustomerAddress" placeholder="Street address" :error="$errors->first('newCustomerAddress')" />
                        <div class="flex justify-end">
                            <x-button variant="primary" wire:click="saveNewCustomer">Save Customer</x-button>
                        </div>
                    </div>
                @else
                    <x-select label="Customer" wire:model="customerId" name="customerId" :options="$this->getCustomerOptions()" placeholder="Search customer..." required :error="$errors->first('customerId')" />
                @endif
            </div>

            {{-- Vehicle --}}
            @if ($customerId)
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">Vehicle</h3>
                        <button wire:click="$set('showNewVehicleForm', {{ $showNewVehicleForm ? 'false' : 'true' }})" type="button" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                            {{ $showNewVehicleForm ? '← Select Existing' : '+ New Vehicle' }}
                        </button>
                    </div>
                    @if ($showNewVehicleForm)
                        <div class="space-y-3">
                            <x-input label="License Plate" wire:model="newVehiclePlate" name="newVehiclePlate" placeholder="L 1234 AB" required :error="$errors->first('newVehiclePlate')" />
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <x-select label="Brand" wire:model="newVehicleBrandId" name="newVehicleBrandId" :options="$this->getBrandOptions()" placeholder="Select brand..." required :error="$errors->first('newVehicleBrandId')" />
                                <x-select label="Model" wire:model="newVehicleModelId" name="newVehicleModelId" :options="$this->getModelOptions()" placeholder="Select model..." required :error="$errors->first('newVehicleModelId')" />
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <x-input label="Year" wire:model="newVehicleYear" name="newVehicleYear" placeholder="2024" :error="$errors->first('newVehicleYear')" />
                                <x-input label="Color" wire:model="newVehicleColor" name="newVehicleColor" placeholder="White" :error="$errors->first('newVehicleColor')" />
                            </div>
                            <div class="flex justify-end">
                                <x-button variant="primary" wire:click="saveNewVehicle">Save Vehicle</x-button>
                            </div>
                        </div>
                    @else
                        <x-select label="Vehicle" wire:model="vehicleId" name="vehicleId" :options="$this->getVehicleOptions()" placeholder="Select vehicle..." required :error="$errors->first('vehicleId')" />
                    @endif
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" wire:click="nextStep">Continue →</x-button>
            </div>
        </div>
    @endif

    {{-- Step 3: Add Items --}}
    @if ($currentStep === 3)
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Add Items</h2>
                    <p class="mt-1 text-sm text-gray-500">Add glass products and/or services to this transaction.</p>
                </div>
                <button wire:click="prevStep" type="button" class="text-sm font-medium text-gray-500 hover:text-gray-700">← Back</button>
            </div>
            @error('items') <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-600">{{ $message }}</div> @enderror

            {{-- Glass Item Form --}}
            @if ($type !== 'service_only')
                <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 font-semibold text-gray-900">Add Glass Product</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-select label="Glass Product" wire:model="selectedProductId" name="selectedProductId" :options="$this->getGlassProductOptions()" placeholder="Search glass products..." />
                        @if ($selectedProductId)
                            <x-select label="Stock Lot" wire:model="selectedStockLotId" name="selectedStockLotId" :options="$this->getStockLotOptions()" placeholder="Select lot..." />
                        @endif
                    </div>
                    @if ($selectedStockLotId)
                        @php $lotDetail = $this->getLotStockDetail(); @endphp
                        @if ($lotDetail)
                            <div class="mt-2 rounded-lg bg-gray-50 p-3 text-xs text-gray-600">
                                <strong>Lot {{ $lotDetail['lot_number'] }}</strong> — Purchase cost: Rp {{ number_format($lotDetail['purchase_cost'], 0, ',', '.') }}
                                @foreach ($lotDetail['balances'] as $b)
                                    | {{ $b['rack'] }}: {{ $b['quantity'] }} pcs
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input label="Quantity" wire:model="glassQuantity" name="glassQuantity" type="number" min="1" required :error="$errors->first('glassQuantity')" />
                            <x-input label="Selling Price (per unit)" wire:model="glassUnitPrice" name="glassUnitPrice" type="number" min="0" step="1000" required :error="$errors->first('glassUnitPrice')" />
                        </div>
                        <div class="mt-3 flex justify-end">
                            <x-button variant="primary" wire:click="addGlassItem">Add Glass Item</x-button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Service Item Form --}}
            @if ($type !== 'glass_sale')
                <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 font-semibold text-gray-900">Add Service</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-select label="Service" wire:model="selectedServiceId" name="selectedServiceId" :options="$this->getServiceOptions()" placeholder="Search services..." />
                        <x-select label="Technician" wire:model="selectedTechnicianId" name="selectedTechnicianId" :options="$this->getTechnicianOptions()" placeholder="Select technician (optional)" />
                    </div>
                    @if ($selectedServiceId)
                        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input label="Quantity" wire:model="serviceQuantity" name="serviceQuantity" type="number" min="1" required />
                            <x-input label="Price (per unit)" wire:model="serviceUnitPrice" name="serviceUnitPrice" type="number" min="0" step="1000" required />
                        </div>
                        <div class="mt-3 flex justify-end">
                            <x-button variant="primary" wire:click="addServiceItem">Add Service</x-button>
                        </div>
                    @endif
                </div>
            @endif


            {{-- Items Table --}}
            @if (!empty($items))
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
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
                                    <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($items as $idx => $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $idx + 1 }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item['name'] }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm"><x-badge :variant="$item['type'] === 'glass' ? 'blue' : 'amber'">{{ $item['type'] === 'glass' ? 'Glass' : 'Service' }}</x-badge></td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900">{{ $item['quantity'] }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900">{{ 'Rp ' . number_format($item['unit_price'], 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ 'Rp ' . number_format($item['total_price'], 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-center">
                                            <button wire:click="removeItem({{ $idx }})" type="button" class="text-red-500 hover:text-red-700">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="w-full max-w-sm space-y-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-900">{{ 'Rp ' . number_format($total, 0, ',', '.') }}</span>
                        </div>
                        @if ($estimatedProfit > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Est. Profit</span>
                                <span class="font-medium text-green-600">{{ 'Rp ' . number_format($estimatedProfit, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" wire:click="nextStep">Continue to Review →</x-button>
            </div>
        </div>

    {{-- Step 4: Review --}}
    @if ($currentStep === 4)
        <div class="mx-auto max-w-3xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Review Transaction</h2>
                    <p class="mt-1 text-sm text-gray-500">Confirm all details before creating.</p>
                </div>
                <button wire:click="prevStep" type="button" class="text-sm font-medium text-gray-500 hover:text-gray-700">← Back</button>
            </div>
            @error('items') <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-600">{{ $message }}</div> @enderror

            <div class="space-y-4">
                {{-- Summary --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-gray-500">Type:</span> <span class="ml-1 font-medium">{{ $this->getTypeLabel() }}</span></div>
                        <div><span class="text-gray-500">Customer:</span> <span class="ml-1 font-medium">{{ optional($transaction ?? null)->customer->name ?? '' }}</span></div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-3 font-semibold text-gray-900">Notes (optional)</h3>
                    <textarea wire:model="notes" rows="3" class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Add any additional notes..."></textarea>
                </div>

                {{-- Items Summary --}}
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Item</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($items as $idx => $item)
                                    <tr>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $idx + 1 }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item['name'] }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-900">{{ $item['quantity'] }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ 'Rp ' . number_format($item['total_price'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                        <div class="flex justify-between text-sm font-semibold">
                            <span>Total</span>
                            <span>{{ 'Rp ' . number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" wire:click="createTransaction" class="bg-green-600 hover:bg-green-500">✓ Create Transaction</x-button>
            </div>
        </div>
    @endif
</div>