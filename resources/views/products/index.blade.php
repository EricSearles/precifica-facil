<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Catálogo comercial</p>
            <p class="page-subtitle">Acompanhe margem, rendimento, status, preço sugerido e preços por canal em uma visão organizada para decisões rápidas.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('products.create') }}" class="button-primary">Novo produto</a>
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
                    <h3 class="table-title">Cadastro de produtos</h3>
                    <p class="table-description">Produtos com categoria, margem, rendimento, status e canais de venda vinculados.</p>
                </div>
                <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center">
                    <form method="GET" action="{{ route('products.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Buscar produto ou categoria" class="block w-full sm:w-64">
                        <select name="category_id" class="block w-full sm:w-48">
                            <option value="0">Todas as categorias</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) $filters['category_id'] === (int) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="block w-full sm:w-44">
                            <option value="">Todos os status</option>
                            <option value="active" @selected($filters['status'] === 'active')>Somente ativos</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Somente inativos</option>
                        </select>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="button-secondary">Aplicar filtros</button>
                            @if ($filters['search'] !== '' || $filters['status'] !== '' || (int) $filters['category_id'] > 0)
                                <a href="{{ route('products.index') }}" class="button-secondary">Limpar</a>
                            @endif
                        </div>
                    </form>
                    <span class="badge-neutral">{{ $products->total() }} registro(s)</span>
                </div>
            </div>

            @if ($products->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhum produto encontrado.</p>
                    <p class="mt-2 text-sm text-slate-500">Ajuste os filtros ou cadastre um novo produto para começar a precificar por canal.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th>Margem</th>
                                <th>Preço sugerido</th>
                                <th>Status</th>
                                <th class="table-actions-head">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <div class="entity-title">{{ $product->name }}</div>
                                        <div class="entity-meta">Rendimento: {{ $product->yield_quantity }} {{ $product->sale_unit }}</div>
                                    </td>
                                    <td>{{ $product->category?->name ?? 'Sem categoria' }}</td>
                                    <td>
                                        @if ($product->use_global_margin)
                                            Margem global
                                        @elseif ($product->profit_margin_type === 'percentage')
                                            {{ number_format((float) $product->profit_margin_value, 2, ',', '.') }}%
                                        @else
                                            @money((float) $product->profit_margin_value, $product->company)
                                        @endif
                                    </td>
                                    <td>
                                        @if ((float) $product->suggested_sale_price > 0)
                                            <div class="font-semibold text-slate-900">@money((float) $product->suggested_sale_price, $product->company)</div>
                                        @else
                                            <div class="font-semibold text-slate-900">A calcular</div>
                                        @endif
                                        @if ((float) $product->suggested_sale_price > 0 && $product->productChannelPrices->isNotEmpty())
                                            <div class="channel-price-list">
                                                @foreach ($product->productChannelPrices->take(3) as $channelPrice)
                                                    <div class="channel-price-item">
                                                        <span class="channel-price-name">{{ $channelPrice->salesChannel?->name ?? 'Canal' }}</span>
                                                        <span class="channel-price-value">@money((float) $channelPrice->channel_price, $product->company)</span>
                                                    </div>
                                                @endforeach
                                                @if ($product->productChannelPrices->count() > 3)
                                                    <div class="entity-meta">+ {{ $product->productChannelPrices->count() - 3 }} canal(is)</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="{{ $product->is_active ? 'badge-success' : 'badge-neutral' }}">
                                            {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions-wrap">
                                            <a href="{{ route('products.edit', $product->id) }}" class="button-table-action">Editar</a>
                                            <a href="{{ route('products.edit', $product->id) }}#product-channels-section" class="button-table-action">Ver canais</a>
                                            <form method="POST" action="{{ route('products.duplicate', $product->id) }}">
                                                @csrf
                                                <button type="submit" class="button-table-action">Duplicar</button>
                                            </form>
                                            <form method="POST" action="{{ route('products.destroy', $product->id) }}" onsubmit="return confirm('Deseja remover este produto?');">
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
                    {{ $products->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
