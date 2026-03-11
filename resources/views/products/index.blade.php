<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Catálogo comercial</p>
            <h2 class="page-title">Produtos prontos para precificação e venda.</h2>
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

        <section class="table-shell">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Cadastro de produtos</h3>
                    <p class="table-description">Produtos com categoria, margem, rendimento, status e canais de venda vinculados.</p>
                </div>
                <span class="badge-neutral">{{ $products->count() }} registro(s)</span>
            </div>

            @if ($products->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhum produto cadastrado ainda.</p>
                    <p class="mt-2 text-sm text-slate-500">Comece criando seu primeiro produto para depois vincular receita, embalagem e canais.</p>
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
                                            R$ {{ number_format((float) $product->profit_margin_value, 2, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-semibold text-slate-900">@money((float) $product->suggested_sale_price, $product->company)</div>
                                        @if ($product->productChannelPrices->isNotEmpty())
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
            @endif
        </section>
    </div>
</x-app-layout>