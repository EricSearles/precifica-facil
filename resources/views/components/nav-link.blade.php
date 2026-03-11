@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out'
            : 'inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="border-color: {{ ($active ?? false) ? 'var(--pf-primary)' : 'transparent' }}; color: {{ ($active ?? false) ? 'var(--pf-text)' : 'var(--pf-text-soft)' }};">
    {{ $slot }}
</a>