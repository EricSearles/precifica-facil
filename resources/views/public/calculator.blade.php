<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Calculadora de preço de venda para alimentos | Precifica Fácil</title>
    <meta name="description" content="Calculadora de preço de venda para alimentos. Descubra quanto cobrar pelo seu produto, simule margem, embalagem, outros custos e taxa de canal em segundos.">
    <link rel="canonical" href="{{ route('calculator.public') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="welcome-shell">
    <header class="welcome-nav">
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" class="brand-mark">
                <x-application-logo class="h-6 w-6 fill-current text-white" />
            </a>
            <div>
                <div class="brand-badge">Calculadora pública</div>
                <p class="mt-3 text-lg font-semibold" style="color: var(--pf-text);">Precifica Fácil</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('terms') }}" class="marketing-link">Termos de Uso</a>
            <a href="{{ route('data-usage') }}" class="marketing-link">Uso de Dados</a>
            <a href="{{ route('login') }}" class="button-secondary">Entrar</a>
            <a href="{{ route('register') }}" class="button-primary">Teste grátis por 14 dias</a>
        </div>
    </header>

    <main class="calculator-page-shell">
        <section class="calculator-hero-grid">
            <div>
                <p class="welcome-kicker">Calculadora preço de venda</p>
                <h1 class="welcome-title">Calculadora de preço de venda para alimentos</h1>
                <p class="welcome-copy">
                    Descubra quanto cobrar pelo seu produto em segundos. Simule custo, margem, embalagem e canais de venda sem precisar criar cadastro.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="button-primary">Teste grátis por 14 dias</a>
                    <a href="{{ route('login') }}" class="button-secondary">Entrar</a>
                </div>

                <div class="calculator-benefits-inline">
                    <span class="calculator-benefit-pill">Sem planilhas</span>
                    <span class="calculator-benefit-pill">Ideal para doces e marmitas</span>
                    <span class="calculator-benefit-pill">Simulação instantânea</span>
                </div>

                <div class="mt-8 max-w-3xl space-y-4">
                    <p class="seo-copy">
                        Se você quer saber como calcular preço de venda, quanto cobrar por um produto alimentício ou como montar a precificação de receitas com mais segurança, esta calculadora ajuda a chegar em um valor sugerido de forma rápida.
                    </p>
                    <p class="seo-copy">
                        Informe o custo total da receita, a quantidade produzida, a embalagem por unidade, outros custos, a margem desejada e a taxa do canal quando houver delivery ou marketplace.
                    </p>
                </div>
            </div>

            <aside class="calculator-hero-card relative overflow-hidden">
                <div class="calculator-hero-orb"></div>
                <div class="relative">
                    <p class="page-kicker">O que você simula aqui</p>
                    <h2 class="mt-3 text-2xl font-semibold" style="color: var(--pf-text);">Preço base, preço final e lucro com uma leitura mais clara.</h2>

                    <div class="mt-6 grid gap-4 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3">
                        <div class="rounded-[24px] border p-4" style="border-color: rgba(37, 99, 235, 0.08); background: rgba(255,255,255,0.92);">
                            <p class="metric-label">1</p>
                            <p class="mt-2 text-base font-semibold" style="color: var(--pf-text);">Custo unitário real</p>
                            <p class="mt-2 text-sm leading-6" style="color: var(--pf-text-soft);">Receita, embalagem e extras distribuídos por unidade.</p>
                        </div>
                        <div class="rounded-[24px] border p-4" style="border-color: rgba(37, 99, 235, 0.08); background: rgba(255,255,255,0.92);">
                            <p class="metric-label">2</p>
                            <p class="mt-2 text-base font-semibold" style="color: var(--pf-text);">Preço base sugerido</p>
                            <p class="mt-2 text-sm leading-6" style="color: var(--pf-text-soft);">Margem aplicada sobre o custo de cada unidade.</p>
                        </div>
                        <div class="rounded-[24px] border p-4" style="border-color: rgba(37, 99, 235, 0.08); background: rgba(255,255,255,0.92);">
                            <p class="metric-label">3</p>
                            <p class="mt-2 text-base font-semibold" style="color: var(--pf-text);">Preço final por canal</p>
                            <p class="mt-2 text-sm leading-6" style="color: var(--pf-text-soft);">Ajuste automático para delivery, app ou marketplace.</p>
                        </div>
                    </div>

                    <div class="mt-8 rounded-[24px] border p-5" style="border-color: rgba(37, 99, 235, 0.08); background: rgba(255,255,255,0.9);">
                        <p class="metric-label">Ideal para</p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="feature-badge">Doces e bolos</span>
                            <span class="feature-badge">Salgados e kits</span>
                            <span class="feature-badge">Marmitas</span>
                            <span class="feature-badge">Delivery</span>
                        </div>
                    </div>
                </div>
            </aside>
        </section>

        <section
            class="mt-12 calculator-main-grid"
            x-data="publicQuickCalculator(@js($calculatorDefaults))"
        >
            <div class="calculator-result-stack calculator-result-column">
                <article class="surface-card-strong">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="page-kicker">Resultado</p>
                            <h2 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Veja o impacto antes de cadastrar.</h2>
                            <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">A simulação mostra o custo por unidade, o preço base sugerido, o preço final do canal e o lucro estimado com a margem desejada.</p>
                        </div>

                        <template x-if="loading">
                            <span class="badge-neutral">Atualizando</span>
                        </template>
                        <template x-if="!loading && result">
                            <span class="badge-success">Cálculo pronto</span>
                        </template>
                        <template x-if="!loading && !result">
                            <span class="badge-neutral">Preencha os campos</span>
                        </template>
                    </div>

                    <div class="mt-8 space-y-5">
                        <div class="calculator-result-hero">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/70">Produto simulado</p>
                                    <p class="mt-3 text-2xl font-semibold text-white" x-text="result?.product_name || form.product_name || 'Sua simulação'"></p>
                                </div>
                                <span class="rounded-full bg-white/14 px-3 py-1 text-xs font-semibold text-white/90" x-text="result?.sales_channel_name || 'Sem taxa de canal'"></span>
                            </div>

                            <div class="mt-8">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/70">Preço sugerido final</p>
                                <p class="mt-3 metric-value-xl" x-text="money(result?.suggested_price)"></p>
                                <p class="mt-3 text-sm leading-6 text-white/78" x-text="result?.sales_channel_name ? 'Valor final ajustado para manter seu líquido mesmo com a taxa do canal.' : 'Valor final para venda direta, sem taxa adicional.'"></p>
                            </div>
                        </div>

                        <div class="calculator-metrics-grid">
                            <div class="calculator-mini-stat">
                                <p class="metric-label">Custo unitário</p>
                                <p class="mt-3 metric-value-lg" x-text="money(result?.unit_cost)"></p>
                            </div>
                            <div class="calculator-mini-stat">
                                <p class="metric-label">Preço base</p>
                                <p class="mt-3 metric-value-lg" x-text="money(result?.base_suggested_price)"></p>
                            </div>
                            <div class="calculator-mini-stat">
                                <p class="metric-label">Lucro por unidade</p>
                                <p class="mt-3 metric-value-lg" x-text="money(result?.profit_per_unit)"></p>
                            </div>
                            <div class="calculator-mini-stat">
                                <p class="metric-label">Preço mínimo</p>
                                <p class="mt-3 metric-value-lg" x-text="money(result?.minimum_price)"></p>
                            </div>
                        </div>

                        <div class="calculator-summary">
                            <p class="metric-label">Resumo do cálculo</p>
                            <dl class="mt-3">
                                <div class="calculator-summary-row">
                                    <dt>Custo total considerado</dt>
                                    <dd class="font-semibold" style="color: var(--pf-text);" x-text="money(result?.total_cost)"></dd>
                                </div>
                                <div class="calculator-summary-row">
                                    <dt>Embalagem total</dt>
                                    <dd class="font-semibold" style="color: var(--pf-text);" x-text="money(result?.packaging_cost)"></dd>
                                </div>
                                <div class="calculator-summary-row">
                                    <dt>Lucro total do lote</dt>
                                    <dd class="font-semibold" style="color: var(--pf-text);" x-text="money(result?.profit_total)"></dd>
                                </div>
                                <div class="calculator-summary-row">
                                    <dt>Margem aplicada</dt>
                                    <dd class="font-semibold" style="color: var(--pf-text);" x-text="percent(result?.profit_margin_percentage ?? form.profit_margin_percentage)"></dd>
                                </div>
                                <div class="calculator-summary-row">
                                    <dt>Canal e taxa</dt>
                                    <dd class="font-semibold" style="color: var(--pf-text);" x-text="result?.channel ? ((result.sales_channel_name || 'Canal informado') + ' · ' + percent(result.channel.percentage_rate)) : 'Sem taxa'"></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </article>

                <article class="surface-card">
                    <p class="page-kicker">Próximo passo</p>
                    <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Quer salvar esse cálculo, organizar receitas e controlar seus custos?</h3>
                    <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">Crie sua conta, inicie seu teste de 14 dias e continue com cadastro de ingredientes, receitas, embalagens e canais de venda dentro do sistema completo.</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="button-primary">Teste grátis por 14 dias</a>
                        <a href="{{ route('login') }}" class="button-secondary">Entrar</a>
                    </div>
                </article>
            </div>

            <article class="calculator-form-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Calculadora rápida</p>
                        <h2 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Simule um preço de venda sem abrir cadastro.</h2>
                        <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">Preencha os campos e acompanhe o cálculo em tempo real. A taxa do canal é opcional e serve para delivery, aplicativo ou marketplace.</p>
                    </div>
                    <span class="badge-accent">Sem login</span>
                </div>

                <div class="mt-6" x-show="error" x-cloak>
                    <div class="flash-error" x-text="error"></div>
                </div>

                <div class="mt-8 space-y-4">
                    <div class="field-block">
                        <x-input-label for="public_product_name" :value="__('Nome da receita/produto')" />
                        <x-text-input id="public_product_name" x-model="form.product_name" @input="queueRefresh" type="text" class="mt-1 block w-full" placeholder="Ex.: Brigadeiro gourmet" />
                        <p class="field-help">Use o nome que o cliente vê no cardápio ou no app.</p>
                    </div>

                    <div class="field-grid-tight">
                        <div class="field-block">
                            <x-input-label for="public_recipe_total_cost" :value="__('Custo total da receita')" />
                            <x-text-input id="public_recipe_total_cost" x-model="form.recipe_total_cost" @input="queueRefresh" type="number" step="0.01" min="0" class="mt-1 block w-full" placeholder="40,00" />
                            <p class="field-help">Soma dos ingredientes ou do lote completo.</p>
                        </div>

                        <div class="field-block">
                            <x-input-label for="public_yield_quantity" :value="__('Quantidade produzida')" />
                            <x-text-input id="public_yield_quantity" x-model="form.yield_quantity" @input="queueRefresh" type="number" step="0.01" min="0.01" class="mt-1 block w-full" placeholder="10" />
                            <p class="field-help">Quantas unidades essa receita gera.</p>
                        </div>
                    </div>

                    <div class="field-grid-tight">
                        <div class="field-block">
                            <x-input-label for="public_packaging_unit_cost" :value="__('Embalagem por unidade')" />
                            <x-text-input id="public_packaging_unit_cost" x-model="form.packaging_unit_cost" @input="queueRefresh" type="number" step="0.01" min="0" class="mt-1 block w-full" placeholder="0,80" />
                            <p class="field-help">Informe somente o valor unitário da embalagem.</p>
                        </div>

                        <div class="field-block">
                            <x-input-label for="public_other_costs" :value="__('Outros custos')" />
                            <x-text-input id="public_other_costs" x-model="form.other_costs" @input="queueRefresh" type="number" step="0.01" min="0" class="mt-1 block w-full" placeholder="5,00" />
                            <p class="field-help">Gás, perda, etiqueta ou qualquer extra do lote.</p>
                        </div>
                    </div>

                    <div class="field-block">
                        <x-input-label for="public_profit_margin_percentage" :value="__('Margem %')" />
                        <x-text-input id="public_profit_margin_percentage" x-model="form.profit_margin_percentage" @input="queueRefresh" type="number" step="0.01" min="0" class="mt-1 block w-full" placeholder="100" />
                        <p class="field-help">A margem define o ganho desejado sobre o custo unitário.</p>
                    </div>

                    <div class="field-block">
                        <x-input-label for="public_sales_channel_name" :value="__('Canal de venda (opcional)')" />
                        <x-text-input id="public_sales_channel_name" x-model="form.sales_channel_name" @input="queueRefresh" type="text" class="mt-1 block w-full" placeholder="Ex.: iFood" />
                        <p class="field-help">Serve para nomear a simulação e deixar o resultado mais claro.</p>
                    </div>

                    <div class="field-grid-tight">
                        <div class="field-block">
                            <x-input-label for="public_channel_percentage_rate" :value="__('Taxa do canal (%)')" />
                            <x-text-input id="public_channel_percentage_rate" x-model="form.channel_percentage_rate" @input="queueRefresh" type="number" step="0.01" min="0" max="99.99" class="mt-1 block w-full" placeholder="12" />
                            <p class="field-help">Deixe em branco para venda direta sem comissão.</p>
                        </div>
                    </div>

                    <div class="calculator-form-footer">
                        <div class="text-sm" style="color: var(--pf-text-soft);">
                            <span x-text="result ? 'Quantidade simulada: ' + quantity(result.yield_quantity) + ' unidade(s)' : 'Preencha os campos para simular.'"></span>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="button-primary">Teste grátis por 14 dias</a>
                            <button
                                type="button"
                                class="button-secondary"
                                @click="form = { product_name: '', recipe_total_cost: '', yield_quantity: '', packaging_unit_cost: '', other_costs: '', profit_margin_percentage: 100, sales_channel_name: '', channel_percentage_rate: '' }; refresh();"
                            >
                                Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="mt-12">
            <div class="max-w-3xl">
                <p class="page-kicker">Benefícios</p>
                <h2 class="mt-2 text-3xl font-semibold" style="color: var(--pf-text);">Entenda sua precificação antes de vender.</h2>
                <p class="mt-4 seo-copy">A calculadora foi pensada para ajudar quem precisa definir preço de venda de receitas e produtos alimentícios com rapidez, sem depender de planilhas complicadas.</p>
            </div>

            <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <article class="feature-card">
                    <span class="feature-badge">Preço ideal</span>
                    <h3 class="mt-3 text-lg font-semibold" style="color: var(--pf-text);">Descubra o preço ideal de venda</h3>
                    <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">Visualize custo unitário, preço base sugerido e lucro por unidade com clareza.</p>
                </article>

                <article class="feature-card">
                    <span class="feature-badge">Custos reais</span>
                    <h3 class="mt-3 text-lg font-semibold" style="color: var(--pf-text);">Considere embalagem e outros custos</h3>
                    <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">Inclua embalagens por unidade e custos extras para chegar mais perto do valor real.</p>
                </article>

                <article class="feature-card">
                    <span class="feature-badge">Canais</span>
                    <h3 class="mt-3 text-lg font-semibold" style="color: var(--pf-text);">Simule canais como delivery e marketplace</h3>
                    <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">Aplique taxa de canal e preserve o valor líquido desejado mesmo com comissão.</p>
                </article>

                <article class="feature-card">
                    <span class="feature-badge">Sistema completo</span>
                    <h3 class="mt-3 text-lg font-semibold" style="color: var(--pf-text);">Salve receitas no sistema completo</h3>
                    <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">Depois da simulação, crie sua conta para organizar receitas, produtos e custos em um só lugar.</p>
                </article>
            </div>
        </section>

        <section class="mt-12 cta-band">
            <p class="page-kicker">Comece agora</p>
            <h2 class="mt-3 text-3xl font-semibold" style="color: var(--pf-text);">Comece grátis e organize sua precificação</h2>
            <p class="mx-auto mt-4 max-w-2xl text-sm leading-7" style="color: var(--pf-text-soft);">Use a calculadora para simular o preço de venda e, quando quiser avançar, entre no sistema com 14 dias de teste para salvar cálculos, receitas e custos da sua operação.</p>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="button-primary">Teste grátis por 14 dias</a>
                <a href="{{ route('login') }}" class="button-secondary">Entrar</a>
            </div>
        </section>
    </main>

    <x-site-footer />
</body>

</html>
