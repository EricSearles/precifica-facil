<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Precifica Facil') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="auth-shell">
            <div class="auth-brand">
                <a href="/" class="brand-mark">
                    <x-application-logo class="h-6 w-6 fill-current text-white" />
                </a>
                <div>
                    <div class="brand-badge">SaaS de precificacao</div>
                    <h1 class="mt-3 text-2xl font-semibold" style="color: var(--pf-text);">Precifica Facil</h1>
                    <p class="auth-muted mt-1">Sistema de gestao e precificacao para pequenos negocios de alimentacao.</p>
                </div>
            </div>

            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>