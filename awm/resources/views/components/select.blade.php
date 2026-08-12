@props(['label' => null, 'name', 'placeholder' => 'Select...', 'disabled' => false, 'required' => false, 'error' => null, 'options' => [], 'value' => null])

<div>
    @if ($label)
        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @disabled($disabled)
        {{ $required ? 'required' : '' }}
        {!! $attributes->merge([
            'class' => 'block w-full rounded-lg border px-3 py-2 text-sm shadow-sm transition-colors focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500 ' . ($error ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 text-gray-900')
        ]) !!}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ $value == $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($error)
        <p class="mt-1 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>