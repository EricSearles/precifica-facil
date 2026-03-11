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

<body class="welcome-shell">
    <header class="welcome-nav">
        <div class="flex items-center gap-4">
            <span class="brand-mark">
                <x-application-logo class="h-6 w-6 fill-current text-white" />
            </span>
            <div>
                <div class="brand-badge">Sistema de precificação</div>
                <h1 class="mt-3 text-lg font-semibold" style="color: var(--pf-text);">Precifica Fácil</h1>
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
            <h2 class="welcome-title">Precifique produtos alimenticios com clareza, velocidade e padrao profissional.</h2>
            <p class="welcome-copy">
                O Precifica Fácil ajuda doceiras, confeiteiras, salgadeiras, marmiteiras e pequenos negocios de alimentacao a organizar ingredientes, receitas, embalagens, canais de venda e preços finais em um painel simples de operar no dia a dia.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                @auth
                <a href="{{ route('dashboard') }}" class="button-primary">Ir para o dashboard</a>
                @else
                <a href="{{ route('register') }}" class="button-primary">Comecar teste gratis</a>
                <a href="{{ route('login') }}" class="button-secondary">Ja tenho conta</a>
                @endauth
            </div>

            @guest
            <p class="mt-4 text-sm" style="color: var(--pf-text-soft);">Teste gratis por 14 dias para montar receitas, precificar produtos e organizar canais de venda.</p>
            @endguest

            <div class="mt-5 flex flex-wrap gap-4 text-sm" style="color: var(--pf-text-soft);">
                <a href="{{ route('terms') }}" class="auth-link">Termos de Uso</a>
                <a href="{{ route('data-usage') }}" class="auth-link">Uso de Dados</a>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="welcome-metric">
                    <p class="metric-label">Receitas</p>
                    <p class="metric-value text-2xl">Composicao completa</p>
                    <p class="metric-caption">Itens, custos extras e embalagem no mesmo calculo.</p>
                </div>
                <div class="welcome-metric">
                    <p class="metric-label">Canais</p>
                    <p class="metric-value text-2xl">preço por venda</p>
                    <p class="metric-caption">iFood, balcao, WhatsApp e outros canais com taxas proprias.</p>
                </div>
                <div class="welcome-metric">
                    <p class="metric-label">Operacao</p>
                    <p class="metric-value text-2xl">Painel simples</p>
                    <p class="metric-caption">Visual limpo para uso rapido mesmo por quem nao e tecnico.</p>
                </div>
            </div>
        </section>

        <aside class="welcome-panel">
            <p class="page-kicker">Fluxo recomendado</p>
            <h3 class="mt-4 text-2xl font-semibold" style="color: var(--pf-text);">Monte a base certa antes de formar o preço.</h3>
            <div class="mt-6 space-y-4">
                <div class="channel-price-item" style="background: #eff6ff;">
                    <span class="channel-price-name">1. Ingredientes</span>
                    <span class="badge-accent">Base tecnica</span>
                </div>
                <div class="channel-price-item">
                    <span class="channel-price-name">2. Produtos e receitas</span>
                    <span class="badge-neutral">Custo e margem</span>
                </div>
                <div class="channel-price-item" style="background: #fff7ed;">
                    <span class="channel-price-name">3. Canais de venda</span>
                    <span class="badge-warning">Taxas e preço final</span>
                </div>
            </div>

            <div class="mt-8 rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #f8fafc;">
                <p class="metric-label">O que o sistema calcula</p>
                <ul class="mt-4 space-y-3 text-sm" style="color: var(--pf-text-soft);">
                    <li>Custo por ingrediente</li>
                    <li>Custo total e unitario da receita</li>
                    <li>preço sugerido do produto</li>
                    <li>preço especifico por canal de venda</li>
                </ul>
            </div>
        </aside>
    </main>

    <x-site-footer />
</body>

</html>
