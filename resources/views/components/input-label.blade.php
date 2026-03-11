@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold tracking-[-0.01em]']) }} style="color: var(--pf-text);">
    {{ $value ?? $slot }}
</label>