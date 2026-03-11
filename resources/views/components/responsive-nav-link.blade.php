@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 ps-3 pe-4 py-2 text-start text-base font-medium transition duration-150 ease-in-out'
            : 'block w-full border-l-4 border-transparent ps-3 pe-4 py-2 text-start text-base font-medium transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="border-color: {{ ($active ?? false) ? 'var(--pf-primary)' : 'transparent' }}; color: {{ ($active ?? false) ? 'var(--pf-primary)' : 'var(--pf-text-soft)' }}; background: {{ ($active ?? false) ? 'rgba(37, 99, 235, 0.08)' : 'transparent' }};">
    {{ $slot }}
</a>