@props([
    'label',
    'value',
    'icon',
    'color' => 'green',
    'sub' => null,
])

@php
    $iconColors = [
        'green' => 'bg-green-100 text-green-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'yellow' => 'bg-yellow-100 text-yellow-700',
        'red' => 'bg-red-100 text-red-700',
        'purple' => 'bg-purple-100 text-purple-700',
    ];
@endphp

<section {{ $attributes->merge(['class' => 'flex min-w-0 items-start justify-between gap-4 rounded-lg border border-gray-200 bg-white p-5 shadow-sm']) }}>
    <div class="min-w-0">
        <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
        <p class="mt-2 break-words text-2xl font-bold text-gray-900">{{ $value }}</p>
        @if ($sub)
            <p class="mt-1 text-xs text-gray-500">{{ $sub }}</p>
        @endif
    </div>
    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-xl {{ $iconColors[$color] ?? $iconColors['green'] }}" aria-hidden="true">{{ $icon }}</span>
</section>