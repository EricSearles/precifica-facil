<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Centro de produção</p>
            <p class="page-subtitle">Acompanhe cada receita com clareza visual, incluindo o reflexo do preço base nos canais de venda vinculados ao produto.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('recipes.create') }}" class="button-primary">Nova receita</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        <section class="table-shell">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Receitas estruturadas</h3>
                    <p class="table-description">Visão consolidada do custo, rendimento, preço sugerido e canais do produto.</p>
                </div>
                <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center">
                    <form method="GET" action="{{ route('recipes.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Buscar receita ou produto" class="block w-full sm:w-64">
                        <select name="product_id" class="block w-full sm:w-56">
                            <option value="0">Todos os produtos</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected((int) $filters['product_id'] === (int) $product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="button-secondary">Aplicar filtros</button>
                            @if ($filters['search'] !== '' || (int) $filters['product_id'] > 0)
                                <a href="{{ route('recipes.index') }}" class="button-secondary">Limpar</a>
                            @endif
                        </div>
                    </form>
                    <span class="badge-neutral">{{ $recipes->total() }} registro(s)</span>
                </div>
            </div>

            @if ($recipes->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhuma receita encontrada.</p>
                    <p class="mt-2 text-sm text-slate-500">Ajuste os filtros ou crie uma nova ficha técnica para acompanhar custo e preço com mais precisão.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Receita</th>
                                <th>Produto</th>
                                <th>Custo total</th>
                                <th>Custo unitário</th>
                                <th>Preço sugerido</th>
                                <th class="table-actions-head">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recipes as $recipe)
                                <tr>
                                    <td>
                                        <div class="entity-title">{{ $recipe->name }}</div>
                                        <div class="entity-meta">Rendimento: {{ $recipe->yield_quantity }} {{ $recipe->yield_unit }}</div>
                                    </td>
                                    <td>{{ $recipe->product?->name ?? 'Sem produto' }}</td>
                                    <td class="font-semibold text-slate-900">@money((float) $recipe->recipe_total_cost, $recipe->company)</td>
                                    <td class="font-semibold text-slate-900">@money((float) $recipe->unit_cost, $recipe->company)</td>
                                    <td>
                                        <div class="font-semibold text-slate-900">@money((float) $recipe->suggested_sale_price, $recipe->company)</div>
                                        @if ($recipe->product?->productChannelPrices?->isNotEmpty())
                                            <div class="channel-price-list">
                                                @foreach ($recipe->product->productChannelPrices->take(3) as $channelPrice)
                                                    <div class="channel-price-item">
                                                        <span class="channel-price-name">{{ $channelPrice->salesChannel?->name ?? 'Canal' }}</span>
                                                        <span class="channel-price-value">@money((float) $channelPrice->channel_price, $recipe->company)</span>
                                                    </div>
                                                @endforeach
                                                @if ($recipe->product->productChannelPrices->count() > 3)
                                                    <div class="entity-meta">+ {{ $recipe->product->productChannelPrices->count() - 3 }} canal(is)</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions-wrap">
                                            <a href="{{ route('recipes.show', $recipe->id) }}" class="button-table-action">Detalhes</a>
                                            <a href="{{ route('recipes.edit', $recipe->id) }}" class="button-table-action">Editar</a>
                                            <form method="POST" action="{{ route('recipes.recalculate', $recipe->id) }}">
                                                @csrf
                                                <button type="submit" class="button-table-action">Recalcular</button>
                                            </form>
                                            <form method="POST" action="{{ route('recipes.duplicate', $recipe->id) }}">
                                                @csrf
                                                <button type="submit" class="button-table-action">Duplicar</button>
                                            </form>
                                            @if ($recipe->product)
                                                <a href="{{ route('products.edit', $recipe->product->id) }}#product-channels-section" class="button-table-action">Ver canais</a>
                                            @endif
                                            <form method="POST" action="{{ route('recipes.destroy', $recipe->id) }}" onsubmit="return confirm('Deseja remover esta receita?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="button-table-action">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t px-6 py-4" style="border-color: var(--pf-border);">
                    {{ $recipes->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
