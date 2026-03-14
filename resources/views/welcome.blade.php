<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Precifica Fácil') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="welcome-shell calculator-public-shell">
    <div class="welcome-stage" aria-hidden="true">
        <div class="welcome-stage-orb welcome-stage-orb-blue"></div>
        <div class="welcome-stage-orb welcome-stage-orb-amber"></div>
        <div class="welcome-stage-orb welcome-stage-orb-slate"></div>
        <div class="welcome-stage-grid"></div>
    </div>

    <header class="welcome-nav">
        <div class="calculator-brand-block">
            <a href="{{ url('/') }}" class="calculator-brand-mark">
                <img src="{{ asset('images/logo-vazado.png') }}" alt="Serales" class="calculator-brand-image">
            </a>
            <div class="calculator-brand-copy">
                <div class="brand-badge calculator-brand-badge">Sistema de precificação</div>
                <p class="calculator-brand-title">Precifica Fácil</p>
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

    <main class="welcome-home">
        <section class="welcome-home-hero">
            <div class="welcome-home-copy">
                <p class="welcome-kicker">Precificação para alimentos</p>
                <h1 class="welcome-home-title">Precificação e gestão para quem vende doces, bolos, salgados e outros produtos alimentícios.</h1>
                <p class="welcome-copy">
                    O Precifica Fácil ajuda a organizar ingredientes, receitas, embalagens, custos extras e preços por canal de venda em um fluxo simples para doceiras, confeiteiras, salgadeiras, marmiteiras e pequenos negócios de alimentação.
                </p>

                <div class="welcome-home-cta">
                    @auth
                    <a href="{{ route('dashboard') }}" class="button-primary">Ir para o painel</a>
                    @else
                    <a href="{{ route('register') }}" class="button-primary">Começar teste grátis</a>
                    <a href="{{ route('calculator.public') }}" class="button-secondary">Usar calculadora grátis</a>
                    @endauth
                </div>

                <div class="welcome-home-points">
                    <span class="calculator-benefit-pill">Custo real por receita</span>
                    <span class="calculator-benefit-pill">Preço sugerido com margem</span>
                    <span class="calculator-benefit-pill">Preço por canal de venda</span>
                </div>
            </div>

            <aside class="welcome-home-summary">
                <div class="welcome-home-summary-card">
                    <p class="page-kicker">Como funciona</p>
                    <h2 class="welcome-home-section-title">Do custo do ingrediente ao preço final.</h2>
                    <div class="mt-6 space-y-3">
                        <div class="channel-price-item welcome-flow-item">
                            <span class="channel-price-name">1. Cadastre ingredientes e embalagens</span>
                            <span class="badge-neutral welcome-flow-badge">Base</span>
                        </div>
                        <div class="channel-price-item">
                            <span class="channel-price-name">2. Monte receitas e custos extras</span>
                            <span class="badge-neutral welcome-flow-badge">Custo</span>
                        </div>
                        <div class="channel-price-item welcome-flow-item">
                            <span class="channel-price-name">3. Defina margem e preço por canal</span>
                            <span class="badge-neutral welcome-flow-badge">Venda</span>
                        </div>
                    </div>

                    <div class="welcome-home-mini-panel">
                        <p class="metric-label">Decisão rápida</p>
                        <p class="mt-3 text-2xl font-semibold" style="color: var(--pf-text);">Veja custo, margem e canal no mesmo fluxo.</p>
                        <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">A proposta é reduzir dúvida na formação de preço e acelerar o ajuste quando o custo muda.</p>
                    </div>
                </div>
            </aside>
        </section>

        <section class="welcome-home-grid">
            <article class="feature-card">
                <p class="page-kicker">Benefícios</p>
                <h2 class="welcome-home-section-title">O que o sistema faz no dia a dia.</h2>
                <div class="mt-6 grid gap-3">
                    <div class="channel-price-item">
                        <span class="channel-price-name">Calcular custo unitário da receita</span>
                        <span class="badge-neutral welcome-flow-badge">Receitas</span>
                    </div>
                    <div class="channel-price-item">
                        <span class="channel-price-name">Sugerir preço com margem</span>
                        <span class="badge-neutral welcome-flow-badge">Preço</span>
                    </div>
                    <div class="channel-price-item">
                        <span class="channel-price-name">Ajustar valor por iFood, balcão ou WhatsApp</span>
                        <span class="badge-neutral welcome-flow-badge">Canais</span>
                    </div>
                    <div class="channel-price-item">
                        <span class="channel-price-name">Organizar ficha técnica da produção</span>
                        <span class="badge-neutral welcome-flow-badge">Operação</span>
                    </div>
                </div>
            </article>

            <article class="feature-card">
                <p class="page-kicker">Para quem é</p>
                <h2 class="welcome-home-section-title">Feito para pequenos negócios de alimentação.</h2>
                <p class="mt-4 seo-copy">
                    O sistema atende quem precisa calcular preço de venda com mais clareza sem depender de planilhas soltas: doceiras, confeiteiras, salgadeiras, produção por encomenda, marmitas e operações pequenas que vendem em mais de um canal.
                </p>
                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="badge-neutral">Doceiras</span>
                    <span class="badge-neutral">Confeiteiras</span>
                    <span class="badge-neutral">Salgadeiras</span>
                    <span class="badge-neutral">Marmitas</span>
                    <span class="badge-neutral">Pequenas fábricas</span>
                </div>
            </article>
        </section>

        <section class="cta-band">
            <p class="page-kicker">Comece agora</p>
            <h2 class="welcome-home-section-title">Use a calculadora grátis ou organize sua operação completa no painel.</h2>
            <p class="mx-auto mt-4 max-w-3xl seo-copy">
                Se você quer entender custo, margem e preço de venda com mais segurança, o Precifica Fácil reúne cálculo de receita, embalagem, custo extra e preço por canal em um sistema simples para produção e venda de alimentos.
            </p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('calculator.public') }}" class="button-secondary">Abrir calculadora pública</a>
                @guest
                <a href="{{ route('register') }}" class="button-primary">Criar conta</a>
                @else
                <a href="{{ route('dashboard') }}" class="button-primary">Abrir painel</a>
                @endguest
            </div>
        </section>
    </main>

    <x-site-footer />
</body>

</html>
