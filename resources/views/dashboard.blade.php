<x-app-layout>
    @php
    $input = [
    'product_name' => old('product_name', $calculatorInput['product_name'] ?? ''),
    'recipe_total_cost' => old('recipe_total_cost', $calculatorInput['recipe_total_cost'] ?? ''),
    'yield_quantity' => old('yield_quantity', $calculatorInput['yield_quantity'] ?? ''),
    'packaging_cost' => old('packaging_cost', $calculatorInput['packaging_cost'] ?? 0),
    'other_costs' => old('other_costs', $calculatorInput['other_costs'] ?? 0),
    'profit_margin_percentage' => old('profit_margin_percentage', $calculatorInput['profit_margin_percentage'] ?? 100),
    'sales_channel_id' => old('sales_channel_id', $calculatorInput['sales_channel_id'] ?? ''),
    ];
    @endphp

    <x-slot name="header">
        <div>
            <p class="page-kicker">Painel principal</p>
            <!-- <h2 class="page-title">Acompanhe seus custos e organize a precificação da empresa.</h2> -->
            <!-- <p class="page-subtitle">Este painel foi pensado para o uso diario: visao rápida do que ja esta estruturado e atalhos para alimentar a base do calculo sem perder tempo.</p> -->
        </div>

        <div class="page-actions">
            <a href="#quick-price-calculator" class="button-primary">Calcular preço rapido</a>
            <a href="{{ route('recipes.index') }}" class="button-secondary">Abrir receitas</a>
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
                        <h3 class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">Monte o preço do jeito certo, sem pular etapas.</h3>
                        <p class="mt-3 max-w-2xl text-sm leading-6" style="color: var(--pf-text-soft);">A ordem que mais funciona no dia a dia e simples: cadastre insumos, organize os produtos, monte as receitas e so entao refine com custos extras e embalagens.</p>
                    </div>
                    <span class="badge-neutral">Sistema de gestao</span>
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
                <p class="page-kicker">Proximas ações</p>
                <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Checklist operacional</h3>
                <div class="mt-6 space-y-4">
                    <div class="rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <p class="text-sm font-semibold" style="color: var(--pf-text);">1. Base de insumos</p>
                        <p class="mt-1 text-sm" style="color: var(--pf-text-soft);">Garanta que os ingredientes tenham unidade, quantidade e preço corretos.</p>
                    </div>
                    <div class="rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <p class="text-sm font-semibold" style="color: var(--pf-text);">2. Receita completa</p>
                        <p class="mt-1 text-sm" style="color: var(--pf-text-soft);">Adicione itens, custos extras e embalagens antes de validar o preço sugerido.</p>
                    </div>
                    <div class="rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <p class="text-sm font-semibold" style="color: var(--pf-text);">3. Revisao comercial</p>
                        <p class="mt-1 text-sm" style="color: var(--pf-text-soft);">Confira margem, rendimento e status do produto para manter a operacao saudavel.</p>
                    </div>
                </div>
            </article>
        </section>

        <section id="quick-price-calculator" class="grid gap-6 lg:grid-cols-2 lg:items-start">
            <article class="surface-card-strong">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Resultado</p>
                        <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Veja o impacto antes de cadastrar.</h3>
                        <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">O preço base considera custo por unidade mais margem. Quando houver canal, o sistema recalcula o valor final para preservar o liquido desejado.</p>
                    </div>
                    @if ($calculatorResult)
                    <span class="badge-success">Calculo pronto</span>
                    @else
                    <span class="badge-neutral">Aguardando dados</span>
                    @endif
                </div>

                @if ($calculatorResult)
                @if ($calculatorResult['product_name'])
                <div class="mt-8 rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                    <p class="metric-label">Produto simulado</p>
                    <p class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">{{ $calculatorResult['product_name'] }}</p>
                </div>
                @endif

                <div class="mt-8 space-y-4">
                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Custo unitario</p>
                            <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['unit_cost'], auth()->user()->company)</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">Custo total dividido pelo rendimento informado.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">preço sugerido</p>
                            <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['suggested_price'], auth()->user()->company)</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">
                            {{ $calculatorResult['sales_channel_name'] ? 'preço ajustado para '.$calculatorResult['sales_channel_name'].'.' : 'preço base para venda sem taxa.' }}
                        </p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Lucro por unidade</p>
                            <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['profit_per_unit'], auth()->user()->company)</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">Liquido estimado apos descontar o custo unitario.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Lucro total</p>
                            <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['profit_total'], auth()->user()->company)</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">Lucro estimado no lote com o rendimento informado.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Canal e taxas</p>
                        @if ($calculatorResult['channel'])
                        <dl class="mt-4 space-y-3 text-sm" style="color: var(--pf-text-soft);">
                            <div class="flex items-center justify-between gap-3">
                                <dt>Canal</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">{{ $calculatorResult['sales_channel_name'] }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Taxa percentual</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">{{ number_format((float) $calculatorResult['channel']['percentage_rate'], 2, ',', '.') }}%</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Taxa total</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['channel']['fee_total'], auth()->user()->company)</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Liquido preservado</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['channel']['net_value'], auth()->user()->company)</dd>
                            </div>
                        </dl>
                        @else
                        <p class="mt-4 text-sm leading-6" style="color: var(--pf-text-soft);">Nenhum canal selecionado. O preço sugerido corresponde ao valor base para balcao, retirada ou vendas sem taxa adicional.</p>
                        @endif
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Resumo do calculo</p>
                        <dl class="mt-4 space-y-3 text-sm" style="color: var(--pf-text-soft);">
                            <div class="flex items-center justify-between gap-3">
                                <dt>Custo total considerado</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['total_cost'], auth()->user()->company)</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>preço minimo sem lucro</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['minimum_price'], auth()->user()->company)</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>preço base no balcao</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">@money((float) $calculatorResult['base_suggested_price'], auth()->user()->company)</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Margem aplicada</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">{{ number_format((float) $calculatorResult['profit_margin_percentage'], 2, ',', '.') }}%</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                @else
                <div class="mt-8 space-y-4">
                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Receita teste</p>
                        <p class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Calculadora rápida</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Custo unitario</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">R$ 0,00</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">Aguardando os dados do calculo.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">preço sugerido</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">R$ 0,00</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">Preencha o formulario para gerar a sugestao.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Lucro por unidade</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">R$ 0,00</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">Mostra o ganho estimado por item.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Lucro total</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">R$ 0,00</p>
                        <p class="mt-2 text-sm" style="color: var(--pf-text-soft);">Mostra o ganho estimado do lote.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Canal e taxas</p>
                        <p class="mt-4 text-sm leading-6" style="color: var(--pf-text-soft);">Nenhum canal selecionado. O preço ajustado por taxa aparecera aqui depois do calculo.</p>
                    </div>

                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Resumo do calculo</p>
                        <dl class="mt-4 space-y-3 text-sm" style="color: var(--pf-text-soft);">
                            <div class="flex items-center justify-between gap-3">
                                <dt>Custo total considerado</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">R$ 0,00</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>preço minimo sem lucro</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">R$ 0,00</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>preço base no balcao</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">R$ 0,00</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <dt>Margem aplicada</dt>
                                <dd class="font-semibold" style="color: var(--pf-text);">0,00%</dd>
                            </div>
                        </dl>
                    </div>
                </div>
                @endif
            </article>

            <article class="form-section">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Calculadora rápida</p>
                        <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Simule um preço de venda sem abrir cadastro.</h3>
                        <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">Informe custo da receita, rendimento, embalagem, outros custos e margem. Se escolher um canal, o sistema ajusta o preço para manter o valor liquido desejado.</p>
                    </div>
                    <span class="badge-accent">Sem salvar</span>
                </div>

                @if ($calculatorError)
                <div class="flash-error mt-6">{{ $calculatorError }}</div>
                @endif

                <form method="POST" action="{{ route('dashboard.quick-price') }}" class="mt-8 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="product_name" :value="__('Nome da receita/produto')" />
                        <x-text-input id="product_name" name="product_name" type="text" class="mt-1 block w-full" :value="$input['product_name']" placeholder="Ex.: Brigadeiro gourmet" />
                        <x-input-error :messages="$errors->get('product_name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="recipe_total_cost" :value="__('Custo total da receita')" />
                        <x-text-input id="recipe_total_cost" name="recipe_total_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$input['recipe_total_cost']" required />
                        <x-input-error :messages="$errors->get('recipe_total_cost')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="yield_quantity" :value="__('Quantidade produzida')" />
                        <x-text-input id="yield_quantity" name="yield_quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="$input['yield_quantity']" required />
                        <x-input-error :messages="$errors->get('yield_quantity')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="profit_margin_percentage" :value="__('Margem %')" />
                        <x-text-input id="profit_margin_percentage" name="profit_margin_percentage" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$input['profit_margin_percentage']" required />
                        <x-input-error :messages="$errors->get('profit_margin_percentage')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="sales_channel_id" :value="__('Canal de venda')" />
                        <select id="sales_channel_id" name="sales_channel_id" class="mt-1 block w-full">
                            <option value="">Balcao / sem taxa</option>
                            @foreach ($salesChannels as $salesChannel)
                            <option value="{{ $salesChannel->id }}" @selected((string) $input['sales_channel_id']===(string) $salesChannel->id)>
                                {{ $salesChannel->name }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sales_channel_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="packaging_cost" :value="__('Embalagem')" />
                        <x-text-input id="packaging_cost" name="packaging_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$input['packaging_cost']" />
                        <x-input-error :messages="$errors->get('packaging_cost')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="other_costs" :value="__('Outros custos')" />
                        <x-text-input id="other_costs" name="other_costs" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$input['other_costs']" />
                        <x-input-error :messages="$errors->get('other_costs')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                        <a href="{{ route('dashboard') }}#quick-price-calculator" class="button-secondary">Limpar</a>
                        <button type="submit" class="button-primary">Calcular preço rapido</button>
                    </div>
                </form>
            </article>
        </section>
    </div>
</x-app-layout>
