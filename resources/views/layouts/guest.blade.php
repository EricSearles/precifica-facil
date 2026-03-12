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

<body class="welcome-shell">
    <div class="auth-shell">
        <div class="auth-brand brand-stack">
            <a href="/" class="brand-image-link">
                <img src="{{ asset('images/logo-serales-pequeno.png') }}" alt="Serales" class="brand-image">
            </a>
            <div class="brand-copy">
                <div class="brand-badge">Sistema de precificação</div>
                <h1 class="brand-title">Precifica Fácil</h1>
                <p class="auth-muted mt-1">Sistema de gestão e precificação para pequenos negocios de alimentacao.</p>
            </div>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>

        <div class="mx-auto mt-6 flex w-full max-w-md flex-wrap items-center justify-center gap-4 text-sm" style="color: var(--pf-text-soft);">
            <a href="{{ route('terms') }}" class="auth-link">Termos de Uso</a>
            <a href="{{ route('data-usage') }}" class="auth-link">Uso de Dados</a>
        </div>

        <x-site-footer />
    </div>
</body>

</html>
