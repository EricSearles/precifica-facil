<x-app-layout>
    @php
        $companyId = auth()->user()->company_id;
        $metrics = [
            ['label' => 'Ingredientes ativos', 'value' => \App\Models\Ingredient::where('company_id', $companyId)->where('is_active', true)->count(), 'caption' => 'Base da sua ficha técnica e custo de produção.'],
            ['label' => 'Produtos cadastrados', 'value' => \App\Models\Product::where('company_id', $companyId)->count(), 'caption' => 'Itens vendidos com margem, rendimento e embalagem.'],
            ['label' => 'Receitas estruturadas', 'value' => \App\Models\Recipe::where('company_id', $companyId)->count(), 'caption' => 'Composições prontas para cálculo e revisão rápida.'],
            ['label' => 'Custos extras', 'value' => \App\Models\ExtraCost::where('company_id', $companyId)->count(), 'caption' => 'Gás, perdas, taxas e demais impactos indiretos.'],
        ];

        $quickLinks = [
            ['title' => 'Cadastrar ingrediente', 'description' => 'Monte a base dos insumos com custo de compra e unidade padrão.', 'route' => route('ingredients.create')],
            ['title' => 'Criar produto', 'description' => 'Defina margem, rendimento e prepare o item para formação de preço.', 'route' => route('products.create')],
            ['title' => 'Montar receita', 'description' => 'Vincule ingredientes, custos extras e embalagem para fechar o custo real.', 'route' => route('recipes.create')],
        ];
    @endphp

    <x-slot name="header">
        <div>
            <p class="page-kicker">Painel principal</p>
            <h2 class="page-title">Acompanhe seus custos e organize a precificação da empresa.</h2>
            <p class="page-subtitle">
                Este painel foi pensado para o uso diário: visão rápida do que já está estruturado e atalhos para alimentar a base do cálculo sem perder tempo.
            </p>
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
                        <h3 class="mt-2 text-2xl font-semibold text-slate-950">Monte o preço do jeito certo, sem pular etapas.</h3>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                            A ordem que mais funciona no dia a dia é simples: cadastre insumos, organize os produtos, monte as receitas e só então refine com custos extras e embalagens.
                        </p>
                    </div>
                    <span class="badge-neutral">SaaS de gestão</span>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    @foreach ($quickLinks as $link)
                        <a href="{{ $link['route'] }}" class="rounded-[24px] border border-slate-200/80 bg-slate-50/90 p-5 transition duration-200 ease-out hover:-translate-y-1 hover:bg-white">
                            <p class="text-sm font-semibold text-slate-900">{{ $link['title'] }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500">{{ $link['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </article>

            <article class="surface-card">
                <p class="page-kicker">Próximas ações</p>
                <h3 class="mt-2 text-xl font-semibold text-slate-950">Checklist operacional</h3>
                <div class="mt-6 space-y-4">
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4">
                        <p class="text-sm font-semibold text-slate-900">1. Base de insumos</p>
                        <p class="mt-1 text-sm text-slate-500">Garanta que os ingredientes tenham unidade, quantidade e preço corretos.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4">
                        <p class="text-sm font-semibold text-slate-900">2. Receita completa</p>
                        <p class="mt-1 text-sm text-slate-500">Adicione itens, custos extras e embalagens antes de validar o preço sugerido.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4">
                        <p class="text-sm font-semibold text-slate-900">3. Revisão comercial</p>
                        <p class="mt-1 text-sm text-slate-500">Confira margem, rendimento e status do produto para manter a operação saudável.</p>
                    </div>
                </div>
            </article>
        </section>
    </div>
</x-app-layout>


