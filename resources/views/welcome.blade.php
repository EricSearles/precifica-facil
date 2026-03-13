<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Precifica Fácil') }}</title>

    <link rel="preçonnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="welcome-shell calculator-public-shell">
    <header class="welcome-nav">
        <div class="calculator-brand-block">
            <a href="{{ url('/') }}" class="calculator-brand-mark">
                <img src="{{ asset('images/logo-vazado.png') }}" alt="Serales" class="calculator-brand-image">
            </a>
            <div class="calculator-brand-copy">
                <div class="brand-badge calculator-brand-badge">Sistema de precificação</div>
                <h1 class="calculator-brand-title">Precifica Fácil</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @auth
            <a href="{{ route('dashboard') }}" class="button-primary">Abrir painel</a>
            @else
            <a href="{{ route('login') }}" class="button-secondary">Entrar</a>
            <a href="{{ route('register') }}" class="button-primary">Criar conta</a>
            @endauth
        </div>
    </header>

    <main class="welcome-hero">
        <section>
            <p class="welcome-kicker">Gestão, custo e venda no mesmo fluxo</p>
            <h2 class="welcome-title">Precifique produtos alimentícios com clareza, velocidade e padrão profissional.</h2>
            <p class="welcome-copy">
                O Precifica Fácil ajuda doceiras, confeiteiras, salgadeiras, marmiteiras e pequenos negócios de alimentação a organizar ingredientes, receitas, embalagens, canais de venda e preços finais em um painel simples de operar no dia a dia.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                @auth
                <a href="{{ route('dashboard') }}" class="button-primary">Ir para o dashboard</a>
                @else
                <a href="{{ route('register') }}" class="button-primary">Começar teste grátis</a>
                <a href="{{ route('login') }}" class="button-secondary">Já tenho conta</a>
                @endauth
            </div>

            @guest
            <p class="mt-4 text-sm" style="color: var(--pf-text-soft);">Teste grátis por 14 dias para montar receitas, precificar produtos e organizar canais de venda.</p>
            @endguest

            <div class="mt-5 flex flex-wrap gap-4 text-sm" style="color: var(--pf-text-soft);">
                <a href="{{ route('terms') }}" class="auth-link">Termos de Uso</a>
                <a href="{{ route('data-usage') }}" class="auth-link">Uso de Dados</a>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="welcome-metric">
                    <p class="metric-label">Receitas</p>
                    <p class="metric-value text-2xl">Composição completa</p>
                    <p class="metric-caption">Itens, custos extras e embalagem no mesmo cálculo.</p>
                </div>
                <div class="welcome-metric">
                    <p class="metric-label">Canais</p>
                    <p class="metric-value text-2xl">Preço por venda</p>
                    <p class="metric-caption">iFood, balcão, WhatsApp e outros canais com taxas próprias.</p>
                </div>
                <div class="welcome-metric">
                    <p class="metric-label">Operação</p>
                    <p class="metric-value text-2xl">Painel simples</p>
                    <p class="metric-caption">Visual limpo para uso rápido mesmo por quem não é técnico.</p>
                </div>
            </div>
        </section>

        <aside class="welcome-panel">
            <p class="page-kicker">Fluxo recomendado</p>
            <h3 class="mt-4 text-2xl font-semibold" style="color: var(--pf-text);">Monte a base certa antes de formar o preço.</h3>
            <div class="mt-6 space-y-4">
                <div class="channel-price-item welcome-flow-item">
                    <span class="channel-price-name">1. Ingredientes</span>
                    <span class="badge-neutral welcome-flow-badge">Base técnica</span>
                </div>
                <div class="channel-price-item">
                    <span class="channel-price-name">2. Produtos e receitas</span>
                    <span class="badge-neutral welcome-flow-badge">Custo e margem</span>
                </div>
                <div class="channel-price-item welcome-flow-item">
                    <span class="channel-price-name">3. Canais de venda</span>
                    <span class="badge-neutral welcome-flow-badge">Taxas e preço final</span>
                </div>
            </div>

            <div class="mt-8 rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #f8fafc;">
                <p class="metric-label">O que o sistema calcula</p>
                <ul class="mt-4 space-y-3 text-sm" style="color: var(--pf-text-soft);">
                    <li>Custo por ingrediente</li>
                    <li>Custo total e unitário da receita</li>
                    <li>Preço sugerido do produto</li>
                    <li>Preço específico por canal de venda</li>
                </ul>
            </div>
        </aside>
    </main>

    <x-site-footer />
</body>

</html>
