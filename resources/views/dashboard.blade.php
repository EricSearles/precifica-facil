<x-app-layout>
    @php
        $companyId = auth()->user()->company_id;
        $metrics = [
            ['label' => 'Ingredientes ativos', 'value' => \App\Models\Ingredient::where('company_id', $companyId)->where('is_active', true)->count(), 'caption' => 'Base da sua ficha tecnica e custo de producao.'],
            ['label' => 'Produtos cadastrados', 'value' => \App\Models\Product::where('company_id', $companyId)->count(), 'caption' => 'Itens vendidos com margem, rendimento e embalagem.'],
            ['label' => 'Receitas estruturadas', 'value' => \App\Models\Recipe::where('company_id', $companyId)->count(), 'caption' => 'Composicoes prontas para calculo e revisao rapida.'],
            ['label' => 'Custos extras', 'value' => \App\Models\ExtraCost::where('company_id', $companyId)->count(), 'caption' => 'Gas, perdas, taxas e demais impactos indiretos.'],
        ];

        $quickLinks = [
            ['title' => 'Cadastrar ingrediente', 'description' => 'Monte a base dos insumos com custo de compra e unidade padrao.', 'route' => route('ingredients.create')],
            ['title' => 'Criar produto', 'description' => 'Defina margem, rendimento e prepare o item para formacao de preco.', 'route' => route('products.create')],
            ['title' => 'Montar receita', 'description' => 'Vincule ingredientes, custos extras e embalagem para fechar o custo real.', 'route' => route('recipes.create')],
        ];
    @endphp

    <x-slot name="header">
        <div>
            <p class="page-kicker">Painel principal</p>
            <h2 class="page-title">Acompanhe seus custos e organize a precificacao da empresa.</h2>
            <p class="page-subtitle">Este painel foi pensado para o uso diario: visao rapida do que ja esta estruturado e atalhos para alimentar a base do calculo sem perder tempo.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('recipes.index') }}" class="button-primary">Abrir receitas</a>
            <a href="{{ route('products.index') }}" class="button-secondary">Ver produtos</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($metrics as $metric)
                <article class="metric-card">
                    <p class="metric-label">{{ $metric['label'] }}</p>
                    <p class="metric-value">{{ $metric['value'] }}</p>
                    <p class="metric-caption">{{ $metric['caption'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
            <article class="surface-card-strong">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Fluxo recomendado</p>
                        <h3 class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">Monte o preco do jeito certo, sem pular etapas.</h3>
                        <p class="mt-3 max-w-2xl text-sm leading-6" style="color: var(--pf-text-soft);">A ordem que mais funciona no dia a dia e simples: cadastre insumos, organize os produtos, monte as receitas e so entao refine com custos extras e embalagens.</p>
                    </div>
                    <span class="badge-neutral">SaaS de gestao</span>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    @foreach ($quickLinks as $link)
                        <a href="{{ $link['route'] }}" class="rounded-[24px] border p-5 transition duration-200 ease-out hover:-translate-y-1" style="border-color: var(--pf-border); background: #f8fbff;">
                            <p class="text-sm font-semibold" style="color: var(--pf-text);">{{ $link['title'] }}</p>
                            <p class="mt-2 text-sm leading-6" style="color: var(--pf-text-soft);">{{ $link['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </article>

            <article class="surface-card">
                <p class="page-kicker">Proximas acoes</p>
                <h3 class="mt-2 text-xl font-semibold" style="color: var(--pf-text);">Checklist operacional</h3>
                <div class="mt-6 space-y-4">
                    <div class="rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <p class="text-sm font-semibold" style="color: var(--pf-text);">1. Base de insumos</p>
                        <p class="mt-1 text-sm" style="color: var(--pf-text-soft);">Garanta que os ingredientes tenham unidade, quantidade e preco corretos.</p>
                    </div>
                    <div class="rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <p class="text-sm font-semibold" style="color: var(--pf-text);">2. Receita completa</p>
                        <p class="mt-1 text-sm" style="color: var(--pf-text-soft);">Adicione itens, custos extras e embalagens antes de validar o preco sugerido.</p>
                    </div>
                    <div class="rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <p class="text-sm font-semibold" style="color: var(--pf-text);">3. Revisao comercial</p>
                        <p class="mt-1 text-sm" style="color: var(--pf-text-soft);">Confira margem, rendimento e status do produto para manter a operacao saudavel.</p>
                    </div>
                </div>
            </article>
        </section>
    </div>
</x-app-layout>