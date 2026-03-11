<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.16em] text-white transition duration-200 ease-out']) }} style="background: var(--pf-danger); border: 1px solid var(--pf-danger);">
    {{ $slot }}
</button>