<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-2xl border border-teal-700/10 bg-gradient-to-r from-teal-50 to-lime-50 px-4 py-2.5 text-xs font-semibold uppercase tracking-[0.18em] text-teal-900 shadow-sm transition duration-200 ease-out hover:-translate-y-0.5 hover:bg-white']) }}>
    {{ $slot }}
</button>


