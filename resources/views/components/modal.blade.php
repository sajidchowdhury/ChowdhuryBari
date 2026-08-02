@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth] ?? 'sm:max-w-2xl';
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' && (show = true)"
    x-on:close-modal.window="$event.detail === '{{ $name }}' && (show = false)"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-[9999] overflow-y-auto px-4 py-6 sm:px-0"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        class="fixed inset-0 bg-black/60"
        @click="show = false"
    ></div>

    {{-- Modal Content --}}
    <div
        x-show="show"
        class="relative mb-6 bg-white rounded-3xl overflow-hidden shadow-2xl sm:w-full {{ $maxWidthClass }} sm:mx-auto"
        @click.stop
    >
        {{ $slot }}
    </div>
</div>
