<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $recipe->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Produto: {{ $recipe->product?->name ?? 'Sem produto vinculado' }}
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('recipes.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Voltar
                </a>
                <a href="{{ route('recipes.edit', $recipe->id) }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Editar receita
                </a>
                <form method="POST" action="{{ route('recipes.recalculate', $recipe->id) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                        Recalcular receita
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    @php
        $units = ['un', 'g', 'kg', 'ml', 'l'];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Custo ingredientes</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">R$ {{ number_format((float) $recipe->ingredients_cost_total, 2, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Custos extras</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">R$ {{ number_format((float) $recipe->extra_cost_total, 2, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Custo total</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">R$ {{ number_format((float) $recipe->recipe_total_cost, 2, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-sm text-gray-500">Preco sugerido</p>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">R$ {{ number_format((float) $recipe->suggested_sale_price, 2, ',', '.') }}</p>
                    @if ($recipe->product?->productChannelPrices?->isNotEmpty())
                        <div class="channel-price-list">
                            @foreach ($recipe->product->productChannelPrices->take(3) as $channelPrice)
                                <div class="channel-price-item">
                                    <span class="channel-price-name">{{ $channelPrice->salesChannel?->name ?? 'Canal' }}</span>
                                    <span class="channel-price-value">@money((float) $channelPrice->channel_price, $recipe->company)</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-lg bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Adicionar ingrediente</h3>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('recipe-items.store') }}" class="grid gap-4 md:grid-cols-4">
                                @csrf
                                <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">

                                <div class="md:col-span-2">
                                    <x-input-label for="ingredient_id" :value="__('Ingrediente')" />
                                    <select id="ingredient_id" name="ingredient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach ($ingredients as $ingredient)
                                            <option value="{{ $ingredient->id }}">{{ $ingredient->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('ingredient_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="quantity_used" :value="__('Quantidade')" />
                                    <x-text-input id="quantity_used" name="quantity_used" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('quantity_used')" required />
                                    <x-input-error :messages="$errors->get('quantity_used')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="unit_used" :value="__('Unidade')" />
                                    <select id="unit_used" name="unit_used" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit }}" @selected(old('unit_used', 'un') === $unit)>{{ strtoupper($unit) }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('unit_used')" class="mt-2" />
                                </div>

                                <div class="md:col-span-4 flex justify-end">
                                    <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                                        Adicionar item
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Itens da receita</h3>
                        </div>
                        <div class="p-6">
                            @if ($recipe->items->isEmpty())
                                <p class="text-sm text-gray-600">Esta receita ainda nao possui itens cadastrados.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach ($recipe->items as $item)
                                        <form method="POST" action="{{ route('recipe-items.update', $item->id) }}" class="rounded-lg border border-gray-200 p-4">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid gap-4 md:grid-cols-5">
                                                <div class="md:col-span-2">
                                                    <x-input-label :value="__('Ingrediente')" />
                                                    <select name="ingredient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        @foreach ($ingredients as $ingredient)
                                                            <option value="{{ $ingredient->id }}" @selected($item->ingredient_id === $ingredient->id)>{{ $ingredient->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Quantidade')" />
                                                    <x-text-input name="quantity_used" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="$item->quantity_used" required />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Unidade')" />
                                                    <select name="unit_used" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        @foreach ($units as $unit)
                                                            <option value="{{ $unit }}" @selected($item->unit_used === $unit)>{{ strtoupper($unit) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="text-sm text-gray-700">
                                                    <p class="font-medium text-gray-900">Custo atual</p>
                                                    <p class="mt-1">Unitario: R$ {{ number_format((float) $item->unit_cost_snapshot, 2, ',', '.') }}</p>
                                                    <p>Total: R$ {{ number_format((float) $item->total_cost, 2, ',', '.') }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-4 flex justify-end gap-3">
                                                <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50">
                                                    Salvar item
                                                </button>
                                            </div>
                                        </form>

                                        <form method="POST" action="{{ route('recipe-items.destroy', $item->id) }}" class="flex justify-end" onsubmit="return confirm('Deseja remover este item da receita?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50">
                                                Remover item
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-lg bg-white shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-100 px-6 py-4">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Custos extras</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <form method="POST" action="{{ route('extra-costs.store') }}" class="grid gap-4 md:grid-cols-3">
                                @csrf
                                <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">

                                <div>
                                    <x-input-label for="description" :value="__('Descricao')" />
                                    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description')" required />
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="type" :value="__('Tipo')" />
                                    <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="fixed" @selected(old('type', 'fixed') === 'fixed')>Valor fixo</option>
                                        <option value="percentage" @selected(old('type') === 'percentage')>Percentual</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="value" :value="__('Valor')" />
                                    <x-text-input id="value" name="value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('value')" required />
                                    <x-input-error :messages="$errors->get('value')" class="mt-2" />
                                </div>

                                <div class="md:col-span-3 flex justify-end">
                                    <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
                                        Adicionar custo extra
                                    </button>
                                </div>
                            </form>

                            @if ($recipe->extraCosts->isEmpty())
                                <p class="text-sm text-gray-600">Nenhum custo extra cadastrado para esta receita.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach ($recipe->extraCosts as $extraCost)
                                        <form method="POST" action="{{ route('extra-costs.update', $extraCost->id) }}" class="rounded-lg border border-gray-200 p-4">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid gap-4 md:grid-cols-4">
                                                <div>
                                                    <x-input-label :value="__('Descricao')" />
                                                    <x-text-input name="description" type="text" class="mt-1 block w-full" :value="$extraCost->description" required />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Tipo')" />
                                                    <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        <option value="fixed" @selected($extraCost->type === 'fixed')>Valor fixo</option>
                                                        <option value="percentage" @selected($extraCost->type === 'percentage')>Percentual</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Valor')" />
                                                    <x-text-input name="value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$extraCost->value" required />
                                                </div>
                                                <div class="text-sm text-gray-700">
                                                    <p class="font-medium text-gray-900">Impacto</p>
                                                    <p class="mt-1">
                                                        @if ($extraCost->type === 'percentage')
                                                            {{ number_format((float) $extraCost->value, 2, ',', '.') }}% sobre ingredientes
                                                        @else
                                                            R$ {{ number_format((float) $extraCost->value, 2, ',', '.') }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-end gap-3">
                                                <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50">
                                                    Salvar custo extra
                                                </button>
                                            </div>
                                        </form>

                                        <form method="POST" action="{{ route('extra-costs.destroy', $extraCost->id) }}" class="flex justify-end" onsubmit="return confirm('Deseja remover este custo extra?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50">
                                                Remover custo extra
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-lg bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Resumo</h3>
                        <dl class="mt-4 space-y-3 text-sm text-gray-700">
                            <div class="flex justify-between gap-3">
                                <dt>Produto</dt>
                                <dd class="text-right font-medium">{{ $recipe->product?->name ?? 'Sem produto' }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt>Rendimento</dt>
                                <dd class="text-right font-medium">{{ $recipe->yield_quantity }} {{ $recipe->yield_unit }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt>Custos extras</dt>
                                <dd class="text-right font-medium">R$ {{ number_format((float) $recipe->extra_cost_total, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt>Embalagem</dt>
                                <dd class="text-right font-medium">R$ {{ number_format((float) $recipe->packaging_cost_total, 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt>Custo unitario</dt>
                                <dd class="text-right font-medium">R$ {{ number_format((float) $recipe->unit_cost, 2, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if ($recipe->preparation_method || $recipe->notes)
                        <div class="rounded-lg bg-white p-6 shadow-sm sm:rounded-lg">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Observacoes</h3>
                            @if ($recipe->preparation_method)
                                <div class="mt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Modo de preparo</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $recipe->preparation_method }}</p>
                                </div>
                            @endif
                            @if ($recipe->notes)
                                <div class="mt-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Notas</p>
                                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $recipe->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>




