@props(['disabled' => false])

<div x-data="{ visible: false }" class="relative">
    <input
        @disabled($disabled)
        {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200 bg-white px-4 py-2.5 pr-11 text-sm text-slate-700 shadow-sm transition duration-200 ease-out focus:border-transparent focus:outline-none focus:ring-2']) }}
        x-bind:type="visible ? 'text' : 'password'"
    >

    <button
        type="button"
        class="absolute inset-y-0 right-0 inline-flex items-center justify-center px-3 text-slate-500 transition duration-200 ease-out hover:text-slate-700"
        x-on:click="visible = !visible"
        x-bind:aria-label="visible ? 'Ocultar senha' : 'Mostrar senha'"
        x-bind:title="visible ? 'Ocultar senha' : 'Mostrar senha'"
    >
        <svg x-show="!visible" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.438 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        <svg x-show="visible" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A3 3 0 0 0 9 12a3 3 0 0 0 4.42 2.58" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.94 9.94 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.438 0 .644a10.024 10.024 0 0 1-4.423 5.523" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.228 6.228A10.023 10.023 0 0 0 2.037 11.68a1.01 1.01 0 0 0 0 .644C3.423 16.49 7.36 19.5 12 19.5a9.97 9.97 0 0 0 5.147-1.425" />
        </svg>
    </button>
</div>
