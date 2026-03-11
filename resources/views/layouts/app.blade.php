<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Precifica Fácil') }}</title>

    <link rel="preçonnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div x-data="{ sidebarOpen: false }" class="app-shell">
        @include('layouts.navigation')

        <div class="content-shell">
            <header class="topbar-shell">
                <div class="flex items-center gap-3">
                    <button type="button" class="button-secondary lg:hidden" @click="sidebarOpen = true">Menu</button>

                    <div class="topbar-meta">
                        <span class="topbar-chip">Painel de gestao e formacao de preços</span>
                        <!-- <span class="hidden md:inline">Painel de gestao e formacao de preços</span> -->
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden md:flex md:items-center md:gap-3 md:text-right">
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--pf-text);">{{ Auth::user()->name }}</p>
                            <p class="text-xs" style="color: var(--pf-text-soft);">{{ Auth::user()->company?->name ?? 'Empresa ativa' }}</p>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="topbar-action">Conta</a>
                </div>
            </header>

            <main class="page-shell">
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
