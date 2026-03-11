<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Gestão comercial</p>
            <h2 class="page-title">Ajuste preço, margem, embalagem e canais do produto.</h2>
            <p class="page-subtitle">Essa tela centraliza as decisões comerciais do item vendido e prepara o produto para refletir o custo da receita em cada canal de venda.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('products.index') }}" class="button-secondary">Voltar aos produtos</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="form-section">
            <div class="mb-6">
                <h3 class="form-section-title">Dados principais do produto</h3>
                <p class="form-section-subtitle">Categoria, unidade de venda, rendimento e configuração de margem.</p>
            </div>

            <form method="POST" action="{{ route('products.update', $product->id) }}">
                @csrf
                @method('PUT')

                @include('products._form', [
                    'product' => $product,
                    'categories' => $categories,
                    'submitLabel' => 'Salvar alterações',
                ])
            </form>
        </section>

        <section class="form-section">
            <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="form-section-title">Embalagens vinculadas</h3>
                    <p class="form-section-subtitle">Cada vínculo atualiza o custo de embalagem do produto e impacta a receita ligada a ele.</p>
                </div>
                <span class="badge-neutral">{{ $productPackagings->count() }} vínculo(s)</span>
            </div>

            <form method="POST" action="{{ route('product-packagings.store') }}" class="field-grid-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div>
                    <x-input-label for="packaging_id" :value="__('Embalagem')" />
                    <select id="packaging_id" name="packaging_id" class="mt-1 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                        @foreach ($packagings as $packaging)
                            <option value="{{ $packaging->id }}">{{ $packaging->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="quantity" :value="__('Quantidade')" />
                    <x-text-input id="quantity" name="quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('quantity', 1)" required />
                </div>

                <div class="flex items-end justify-end">
                    <button type="submit" class="button-primary">Vincular embalagem</button>
                </div>
            </form>

            @if ($productPackagings->isEmpty())
                <div class="empty-state mt-6">
                    <p class="text-base font-semibold text-slate-900">Nenhuma embalagem vinculada ainda.</p>
                    <p class="mt-2 text-sm text-slate-500">Adicione ao menos uma embalagem para refletir esse custo no cálculo da receita.</p>
                </div>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($productPackagings as $productPackaging)
                        <form method="POST" action="{{ route('product-packagings.update', $productPackaging->id) }}" class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-5">
                            @csrf
                            @method('PUT')
                            <div class="grid gap-4 md:grid-cols-4">
                                <div>
                                    <x-input-label :value="__('Embalagem')" />
                                    <select name="packaging_id" class="mt-1 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                                        @foreach ($packagings as $packaging)
                                            <option value="{{ $packaging->id }}" @selected($productPackaging->packaging_id === $packaging->id)>{{ $packaging->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label :value="__('Quantidade')" />
                                    <x-text-input name="quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="$productPackaging->quantity" required />
                                </div>
                                <div class="text-sm text-slate-600">
                                    <p class="font-semibold text-slate-900">Custo calculado</p>
                                    <p class="mt-2">Unitário: @money((float) $productPackaging->packaging?->unit_cost, $product->company)</p>
                                    <p>Total: @money((float) $productPackaging->total_cost, $product->company)</p>
                                </div>
                                <div class="flex items-end justify-end gap-2">
                                    <button type="submit" class="button-secondary">Salvar</button>
                                </div>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('product-packagings.destroy', $productPackaging->id) }}" class="flex justify-end" onsubmit="return confirm('Deseja remover esta embalagem do produto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-secondary">Remover vínculo</button>
                        </form>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="form-section">
            <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="form-section-title">Preços por canal</h3>
                    <p class="form-section-subtitle">Salve um preço específico para iFood, balcão ou qualquer outro canal. O cálculo usa as taxas do canal e o valor líquido desejado para o produto.</p>
                </div>
                <span class="badge-neutral">Preço base atual: @money((float) $product->suggested_sale_price, $product->company)</span>
            </div>

            @if ($salesChannels->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhum canal ativo disponível.</p>
                    <p class="mt-2 text-sm text-slate-500">Cadastre primeiro um canal de venda com suas taxas para começar a salvar preços específicos.</p>
                    <div class="mt-4">
                        <a href="{{ route('sales-channels.create') }}" class="button-primary">Criar canal de venda</a>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('product-channel-prices.store') }}" class="grid gap-4 md:grid-cols-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div>
                        <x-input-label for="sales_channel_id" :value="__('Canal de venda')" />
                        <select id="sales_channel_id" name="sales_channel_id" class="mt-1 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                            @foreach ($salesChannels as $salesChannel)
                                <option value="{{ $salesChannel->id }}" @selected((int) old('sales_channel_id') === $salesChannel->id)>{{ $salesChannel->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sales_channel_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="desired_net_value" :value="__('Valor líquido desejado')" />
                        <x-text-input id="desired_net_value" name="desired_net_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('desired_net_value', $product->suggested_sale_price)" />
                        <x-input-error :messages="$errors->get('desired_net_value')" class="mt-2" />
                    </div>

                    <div class="text-sm text-slate-600">
                        <p class="font-semibold text-slate-900">Como funciona</p>
                        <p class="mt-2">Se deixar igual ao preço base, o sistema calcula quanto cobrar no canal para sobrar o mesmo líquido do preço atual.</p>
                    </div>

                    <div class="flex items-end justify-end">
                        <button type="submit" class="button-primary">Calcular e salvar</button>
                    </div>
                </form>

                @if ($productChannelPrices->isEmpty())
                    <div class="empty-state mt-6">
                        <p class="text-base font-semibold text-slate-900">Nenhum preço por canal salvo ainda.</p>
                        <p class="mt-2 text-sm text-slate-500">Use o formulário acima para gerar e persistir o valor específico do produto em cada canal.</p>
                    </div>
                @else
                    <div class="mt-6 space-y-4">
                        @foreach ($productChannelPrices as $productChannelPrice)
                            @php
                                $percentageRate = $productChannelPrice->salesChannel->fees
                                    ->where('is_active', true)
                                    ->where('type', 'percentage')
                                    ->sum(fn ($fee) => (float) $fee->value);
                            @endphp

                            <div class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-5">
                                <form method="POST" action="{{ route('product-channel-prices.update', $productChannelPrice->id) }}" class="grid gap-4 md:grid-cols-5">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <x-input-label :value="__('Canal')" />
                                        <div class="mt-1 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm">
                                            {{ $productChannelPrice->salesChannel->name }}
                                        </div>
                                    </div>

                                    <div>
                                        <x-input-label :value="__('Valor líquido desejado')" />
                                        <x-text-input name="desired_net_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$productChannelPrice->desired_net_value" />
                                    </div>

                                    <div class="text-sm text-slate-600">
                                        <p class="font-semibold text-slate-900">Taxas</p>
                                        <p class="mt-2">Percentual: {{ number_format((float) $percentageRate, 2, ',', '.') }}%</p>
                                        <p>Fixa: @money((float) $productChannelPrice->fixed_fee_total, $product->company)</p>
                                    </div>

                                    <div class="text-sm text-slate-600">
                                        <p class="font-semibold text-slate-900">Preço calculado</p>
                                        <p class="mt-2">Canal: @money((float) $productChannelPrice->channel_price, $product->company)</p>
                                        <p>Líquido: @money((float) $productChannelPrice->net_value, $product->company)</p>
                                    </div>

                                    <div class="flex items-end justify-end gap-2">
                                        <button type="submit" class="button-secondary">Atualizar</button>
                                    </div>
                                </form>

                                <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 text-sm text-slate-500">
                                    <span>Referência base: @money((float) $productChannelPrice->reference_price, $product->company)</span>
                                    <form method="POST" action="{{ route('product-channel-prices.destroy', $productChannelPrice->id) }}" onsubmit="return confirm('Deseja remover este preço do canal?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-secondary">Remover</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </section>
    </div>
</x-app-layout>