<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Precifica Fácil') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @php
    $company = Auth::user()?->company;
    $accessNotice = $company?->accessNotice();
    @endphp

    <div x-data="{ sidebarOpen: false }" class="app-shell">
        @include('layouts.navigation')

        <div class="content-shell">
            <header class="topbar-shell">
                <div class="flex items-center gap-3">
                    <button type="button" class="button-secondary lg:hidden" @click="sidebarOpen = true">Menu</button>

                    <div class="topbar-meta">
                        <span class="topbar-chip">Painel de gestão e formação de preços</span>
                        @if ($accessNotice)
                        <span class="topbar-chip">{{ $accessNotice['label'] }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex md:items-center md:gap-3 md:text-right">
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--pf-text);">{{ Auth::user()->name }}</p>
                            <p class="text-xs" style="color: var(--pf-text-soft);">{{ $company?->name ?? 'Empresa ativa' }}</p>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="topbar-action">Conta</a>
                </div>
            </header>

            <main class="page-shell">
                @if ($accessNotice)
                <div class="mb-6 rounded-[24px] border px-5 py-4 text-sm" style="border-color: {{ $accessNotice['blocked'] ? 'rgba(220, 38, 38, 0.18)' : 'rgba(245, 158, 11, 0.18)' }}; background: {{ $accessNotice['blocked'] ? 'rgba(254, 242, 242, 0.95)' : 'rgba(255, 251, 235, 0.95)' }}; color: var(--pf-text);">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em]" style="color: {{ $accessNotice['blocked'] ? '#b91c1c' : '#b45309' }};">{{ $accessNotice['label'] }}</p>
                            <p class="mt-2 leading-6" style="color: var(--pf-text-soft);">{{ $accessNotice['message'] }}</p>
                        </div>
                        <a href="{{ route('billing.portal') }}" class="button-secondary text-xs">Abrir Meu plano</a>
                    </div>
                </div>
                @endif

                @isset($header)
                <div class="page-header">
                    {{ $header }}
                </div>
                @endisset

                {{ $slot }}
            </main>

            <x-site-footer />
        </div>
    </div>
</body>

</html>
