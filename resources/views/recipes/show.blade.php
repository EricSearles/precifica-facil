<x-app-layout>
    <x-slot name="header">
        <div class="page-header !mb-0">
            <div>
                <p class="page-kicker">Ficha técnica</p>
                <h2 class="page-title">{{ $recipe->name }}</h2>
                <p class="page-subtitle">Produto vinculado: {{ $recipe->product?->name ?? 'Sem produto vinculado' }}</p>
            </div>

            <div class="page-actions">
                <a href="{{ route('recipes.index') }}" class="button-secondary">Voltar</a>
                <a href="{{ route('recipes.edit', $recipe->id) }}" class="button-secondary">Editar receita</a>
                <form method="POST" action="{{ route('recipes.duplicate', $recipe->id) }}">
                    @csrf
                    <button type="submit" class="button-secondary">Duplicar receita</button>
                </form>
                <form method="POST" action="{{ route('recipes.recalculate', $recipe->id) }}">
                    @csrf
                    <button type="submit" class="button-primary">Recalcular receita</button>
                </form>
            </div>
        </div>
    </x-slot>

    @php
        $units = ['un', 'g', 'kg', 'ml', 'l'];
        $expectedRevenue = (float) $recipe->suggested_sale_price * (float) $recipe->yield_quantity;
        $expectedProfit = $expectedRevenue - (float) $recipe->recipe_total_cost;
        $popularConversions = [
            ['measure' => 'Colher de chá', 'ml' => '5 ML', 'g' => '4,5 G a 5,5 G'],
            ['measure' => 'Colher de sopa', 'ml' => '15 ML', 'g' => '13,5 G a 16,5 G'],
            ['measure' => 'Xícara de chá', 'ml' => '240 ML', 'g' => '216 G a 264 G'],
            ['measure' => 'Copo americano', 'ml' => '190 ML', 'g' => '171 G a 209 G'],
            ['measure' => 'Copo de requeijão', 'ml' => '250 ML', 'g' => '225 G a 275 G'],
            ['measure' => 'Pitada', 'ml' => '-', 'g' => '1 G a 2 G'],
            ['measure' => 'Punhado', 'ml' => '-', 'g' => '30 G a 40 G'],
        ];
    @endphp

    <div class="page-shell">
        <div class="mx-auto max-w-7xl space-y-6">
            @if (session('success'))
                <div class="flash-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="flash-error">{{ session('error') }}</div>
            @endif

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-6">
                <div class="metric-card">
                    <p class="metric-label">Custo ingredientes</p>
                    <p class="metric-value">@money((float) $recipe->ingredients_cost_total, $recipe->company)</p>
                </div>
                <div class="metric-card">
                    <p class="metric-label">Custos extras</p>
                    <p class="metric-value">@money((float) $recipe->extra_cost_total, $recipe->company)</p>
                </div>
                <div class="metric-card">
                    <p class="metric-label">Custo total</p>
                    <p class="metric-value">@money((float) $recipe->recipe_total_cost, $recipe->company)</p>
                </div>
                <div class="metric-card">
                    <p class="metric-label">Preço sugerido</p>
                    <p class="metric-value">@money((float) $recipe->suggested_sale_price, $recipe->company)</p>
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
                <div class="metric-card">
                    <p class="metric-label">Expectativa de receita</p>
                    <p class="metric-value">@money($expectedRevenue, $recipe->company)</p>
                    <p class="metric-caption">Preço sugerido multiplicado pelo rendimento da receita.</p>
                </div>
                <div class="metric-card">
                    <p class="metric-label">Lucro esperado do lote</p>
                    <p class="metric-value">@money($expectedProfit, $recipe->company)</p>
                    <p class="metric-caption">Receita estimada menos o custo total da receita.</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3 lg:items-start">
                <div class="space-y-6 lg:col-span-2">
                    <div class="form-section">
                        <div class="border-b pb-4" style="border-color: var(--pf-border);">
                            <h3 class="form-section-title">Adicionar ingrediente</h3>
                            <p class="form-section-subtitle">Inclua os insumos que compoem a receita e mantenha o custo atualizado. O sistema converte automaticamente a unidade usada na receita para calcular o valor correto do item.</p>
                        </div>
                        @php
                            $selectedIngredient = old('ingredient_id') ? $ingredients->firstWhere('id', (int) old('ingredient_id')) : null;
                            $selectedIngredientLabel = $selectedIngredient
                                ? trim($selectedIngredient->name.($selectedIngredient->brand ? ' · '.$selectedIngredient->brand : ''))
                                : '';
                        @endphp
                        <form
                            method="POST"
                            action="{{ route('recipe-items.store') }}"
                            class="mt-6 grid gap-4 md:grid-cols-4"
                            x-data="recipeIngredientAutocomplete({
                                searchUrl: '{{ route('ingredients.search') }}',
                                createUrl: '{{ route('ingredients.create') }}',
                                unitOptions: @js($ingredientUnitOptions),
                                initialId: '{{ old('ingredient_id', '') }}',
                                initialUnit: '{{ old('unit_used', '') }}',
                                initialLabel: @js($selectedIngredientLabel),
                            })"
                        >
                            @csrf
                            <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                            <input type="hidden" name="ingredient_id" x-model="selectedIngredient">
                            <div class="md:col-span-2">
                                <x-input-label for="ingredient_search" :value="__('Ingrediente')" />
                                <div class="relative mt-1" @click.outside="open = false">
                                    <x-text-input
                                        id="ingredient_search"
                                        type="text"
                                        class="block w-full pr-24"
                                        x-model="search"
                                        @input="queueSearch"
                                        @focus="if (results.length) open = true"
                                        @keydown.arrow-down.prevent="moveHighlight(1)"
                                        @keydown.arrow-up.prevent="moveHighlight(-1)"
                                        @keydown.enter.prevent="confirmHighlight"
                                        @keydown.escape="open = false"
                                        placeholder="Digite para buscar ingrediente"
                                        autocomplete="off"
                                    />
                                    <button
                                        type="button"
                                        class="absolute inset-y-1 right-1 rounded-xl px-3 text-xs font-semibold transition duration-200 ease-out"
                                        style="background: #eef4fb; color: var(--pf-primary);"
                                        @click="clearSelection"
                                        x-show="search || selectedIngredient"
                                        x-cloak
                                    >
                                        Limpar
                                    </button>

                                    <div
                                        class="absolute z-20 mt-2 w-full overflow-hidden rounded-[20px] border bg-white shadow-lg"
                                        style="border-color: var(--pf-border);"
                                        x-show="open"
                                        x-cloak
                                    >
                                        <template x-if="loading">
                                            <div class="px-4 py-3 text-sm" style="color: var(--pf-text-soft);">Buscando ingredientes...</div>
                                        </template>

                                        <template x-if="!loading && results.length">
                                            <div class="max-h-72 overflow-y-auto py-2">
                                                <template x-for="(ingredient, index) in results" :key="ingredient.id">
                                                    <button
                                                        type="button"
                                                        class="flex w-full items-start justify-between gap-3 px-4 py-3 text-left transition duration-150 ease-out"
                                                        :style="highlightedIndex === index ? 'background: #eef4fb;' : ''"
                                                        @mouseenter="highlightedIndex = index"
                                                        @click="selectIngredient(ingredient)"
                                                    >
                                                        <span>
                                                            <span class="block text-sm font-semibold" style="color: var(--pf-text);" x-text="ingredient.name"></span>
                                                            <span class="mt-1 block text-xs" style="color: var(--pf-text-soft);" x-text="ingredient.brand ? ingredient.brand : 'Sem marca informada'"></span>
                                                        </span>
                                                        <span class="text-xs font-semibold uppercase" style="color: var(--pf-primary);" x-text="ingredient.base_unit ? ingredient.base_unit : ''"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>

                                        <template x-if="!loading && !results.length && search.trim().length >= 2">
                                            <div class="px-4 py-4 text-sm" style="color: var(--pf-text-soft);">
                                                <p>Nenhum ingrediente encontrado.</p>
                                                <a :href="createUrl" class="auth-link mt-2 inline-flex">Cadastrar ingrediente</a>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('ingredient_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="quantity_used" :value="__('Quantidade')" />
                                <x-text-input id="quantity_used" name="quantity_used" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('quantity_used')" required />
                                <x-input-error :messages="$errors->get('quantity_used')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="unit_used" :value="__('Unidade')" />
                                <select id="unit_used" name="unit_used" class="mt-1 block w-full" x-model="selectedUnit">
                                    <option value="">Selecione...</option>
                                    <template x-for="unit in (unitOptions[selectedIngredient] || [])" :key="unit">
                                        <option :value="unit" x-text="unit.toUpperCase()"></option>
                                    </template>
                                </select>
                                <x-input-error :messages="$errors->get('unit_used')" class="mt-2" />
                            </div>
                            <div class="md:col-span-4 flex justify-end">
                                <button type="submit" class="button-primary">Adicionar item</button>
                            </div>
                        </form>
                    </div>

                    <div class="surface-card" x-data="{ openConversions: false }">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 text-left"
                            @click="openConversions = !openConversions"
                        >
                            <div>
                                <h3 class="form-section-title">Conversão de medidas</h3>
                                <p class="form-section-subtitle">Tabela rápida para medidas culinárias populares.</p>
                            </div>
                            <span class="badge-neutral" x-text="openConversions ? 'Ocultar' : 'Mostrar'"></span>
                        </button>

                        <div x-show="openConversions" x-cloak class="mt-4 space-y-3">
                            <div class="overflow-x-auto rounded-[20px] border" style="border-color: var(--pf-border);">
                                <table class="data-table min-w-[560px]">
                                    <thead>
                                        <tr>
                                            <th>Medida</th>
                                            <th>ML</th>
                                            <th>G</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($popularConversions as $conversion)
                                            <tr>
                                                <td class="font-medium" style="color: var(--pf-text);">{{ $conversion['measure'] }}</td>
                                                <td class="font-semibold" style="color: var(--pf-text);">{{ $conversion['ml'] }}</td>
                                                <td class="font-semibold" style="color: var(--pf-text);">{{ $conversion['g'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs" style="color: var(--pf-text-soft);">As equivalências em gramas variam conforme densidade e tipo do ingrediente. Use esta tabela como referência prática e prefira pesar quando precisar de maior precisão.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="surface-card">
                        <h3 class="form-section-title">Resumo</h3>
                        <dl class="mt-4 space-y-3 text-sm" style="color: var(--pf-text-soft);">
                            <div class="flex justify-between gap-3"><dt>Produto</dt><dd class="text-right font-semibold" style="color: var(--pf-text);">{{ $recipe->product?->name ?? 'Sem produto' }}</dd></div>
                            <div class="flex justify-between gap-3"><dt>Rendimento</dt><dd class="text-right font-semibold" style="color: var(--pf-text);">{{ $recipe->yield_quantity }} {{ $recipe->yield_unit }}</dd></div>
                            <div class="flex justify-between gap-3"><dt>Custos extras</dt><dd class="text-right font-semibold" style="color: var(--pf-text);">@money((float) $recipe->extra_cost_total, $recipe->company)</dd></div>
                            <div class="flex justify-between gap-3"><dt>Embalagem</dt><dd class="text-right font-semibold" style="color: var(--pf-text);">@money((float) $recipe->packaging_cost_total, $recipe->company)</dd></div>
                            <div class="flex justify-between gap-3"><dt>Custo unitário</dt><dd class="text-right font-semibold" style="color: var(--pf-text);">@money((float) $recipe->unit_cost, $recipe->company)</dd></div>
                            <div class="flex justify-between gap-3"><dt>Expectativa de receita</dt><dd class="text-right font-semibold" style="color: var(--pf-text);">@money($expectedRevenue, $recipe->company)</dd></div>
                            <div class="flex justify-between gap-3"><dt>Lucro esperado do lote</dt><dd class="text-right font-semibold" style="color: var(--pf-text);">@money($expectedProfit, $recipe->company)</dd></div>
                        </dl>
                    </div>

                    @if ($recipe->preparation_method || $recipe->notes)
                        <div class="surface-card">
                            <h3 class="form-section-title">Observacoes</h3>
                            @if ($recipe->preparation_method)
                                <div class="mt-4"><p class="metric-label">Modo de preparo</p><p class="mt-1 whitespace-pre-line text-sm leading-6" style="color: var(--pf-text-soft);">{{ $recipe->preparation_method }}</p></div>
                            @endif
                            @if ($recipe->notes)
                                <div class="mt-4"><p class="metric-label">Notas</p><p class="mt-1 whitespace-pre-line text-sm leading-6" style="color: var(--pf-text-soft);">{{ $recipe->notes }}</p></div>
                            @endif
                        </div>
                    @endif

                </div>
            </div>

            <div class="space-y-6">
                    <div class="form-section">
                        <div class="border-b pb-4" style="border-color: var(--pf-border);">
                            <h3 class="form-section-title">Itens da receita</h3>
                            <p class="form-section-subtitle">Revise quantidades, unidade e impacto de cada ingrediente.</p>
                        </div>
                        <div class="mt-6">
                            @if ($recipe->items->isEmpty())
                                <p class="auth-muted">Esta receita ainda não possui itens cadastrados.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach ($recipe->items as $item)
                                        <form method="POST" action="{{ route('recipe-items.update', $item->id) }}" class="rounded-[24px] border p-4" style="border-color: var(--pf-border); background: #fbfdff;" x-data="{ unitOptions: @js($ingredientUnitOptions), selectedIngredient: '{{ $item->ingredient_id }}', selectedUnit: '{{ $item->unit_used }}' }">
                                            <div x-effect="if (selectedIngredient && !(unitOptions[selectedIngredient] || []).includes(selectedUnit)) { selectedUnit = (unitOptions[selectedIngredient] || [])[0] || ''; }" class="hidden"></div>
                                            @csrf
                                            @method('PUT')
                                            <div class="grid gap-4 md:grid-cols-5">
                                                <div class="md:col-span-2">
                                                    <x-input-label :value="__('Ingrediente')" />
                                                    <select name="ingredient_id" class="mt-1 block w-full" x-model="selectedIngredient">
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
                                                    <select name="unit_used" class="mt-1 block w-full" x-model="selectedUnit">
                                                        <template x-for="unit in (unitOptions[selectedIngredient] || [])" :key="unit">
                                                            <option :value="unit" x-text="unit.toUpperCase()"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div class="text-sm" style="color: var(--pf-text-soft);">
                                                    <p class="font-semibold" style="color: var(--pf-text);">Custo atual</p>
                                                    <div class="detail-stack">
                                                        <p class="detail-line"><span class="detail-label">Unitario:</span><span class="detail-value">@money((float) $item->unit_cost_snapshot, $recipe->company)</span></p>
                                                        <p class="detail-line"><span class="detail-label">Total:</span><span class="detail-value">@money((float) $item->total_cost, $recipe->company)</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-end gap-3">
                                                <button type="submit" class="button-secondary">Salvar item</button>
                                                <button type="submit" form="delete-recipe-item-{{ $item->id }}" class="button-secondary" onclick="return confirm('Deseja remover este item da receita?');">Remover item</button>
                                            </div>
                                        </form>
                                        <form id="delete-recipe-item-{{ $item->id }}" method="POST" action="{{ route('recipe-items.destroy', $item->id) }}" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="border-b pb-4" style="border-color: var(--pf-border);">
                            <h3 class="form-section-title">Custos extras</h3>
                            <p class="form-section-subtitle">Acrescente despesas indiretas e percentuais para aproximar o custo real.</p>
                        </div>
                        <div class="mt-6 space-y-6">
                            <form method="POST" action="{{ route('extra-costs.store') }}" class="grid gap-4 md:grid-cols-4">
                                @csrf
                                <input type="hidden" name="recipe_id" value="{{ $recipe->id }}">
                                <div>
                                    <x-input-label for="description" :value="__('Descricao')" />
                                    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description')" required />
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="type" :value="__('Tipo')" />
                                    <select id="type" name="type" class="mt-1 block w-full">
                                        <option value="fixed" @selected(old('type', 'fixed') === 'fixed')>Valor fixo</option>
                                        <option value="percentage" @selected(old('type') === 'percentage')>Percentual</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="value" :value="__('Valor')" />
                                    <x-text-input id="value" name="value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('value')" />
                                    <x-input-error :messages="$errors->get('value')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="labor_minutes" :value="__('Tempo de mão de obra (min)')" />
                                    <x-text-input id="labor_minutes" name="labor_minutes" type="number" step="1" min="1" class="mt-1 block w-full" :value="old('labor_minutes')" />
                                    <x-input-error :messages="$errors->get('labor_minutes')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="labor_hourly_rate" :value="__('Valor da hora')" />
                                    <x-text-input id="labor_hourly_rate" name="labor_hourly_rate" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('labor_hourly_rate')" />
                                    <x-input-error :messages="$errors->get('labor_hourly_rate')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="monthly_salary" :value="__('Salário mensal')" />
                                    <x-text-input id="monthly_salary" name="monthly_salary" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('monthly_salary')" />
                                    <x-input-error :messages="$errors->get('monthly_salary')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="monthly_hours" :value="__('Horas/mês')" />
                                    <x-text-input id="monthly_hours" name="monthly_hours" type="number" step="1" min="1" class="mt-1 block w-full" :value="old('monthly_hours', 220)" />
                                    <x-input-error :messages="$errors->get('monthly_hours')" class="mt-2" />
                                </div>
                                <div class="md:col-span-4">
                                    <p class="text-xs leading-5" style="color: var(--pf-text-soft);">Se preferir, informe o salário mensal e o sistema calcula automaticamente o valor da hora. O padrão é 220 horas por mês, mas você pode ajustar.</p>
                                </div>
                                <div class="md:col-span-4 flex justify-end">
                                    <button type="submit" class="button-primary">Adicionar custo extra</button>
                                </div>
                            </form>
                            @if ($recipe->extraCosts->isEmpty())
                                <p class="auth-muted">Nenhum custo extra cadastrado para esta receita.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach ($recipe->extraCosts as $extraCost)
                                        <form method="POST" action="{{ route('extra-costs.update', $extraCost->id) }}" class="rounded-[24px] border p-4" style="border-color: var(--pf-border); background: #fbfdff;">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid gap-4 md:grid-cols-5">
                                                <div>
                                                    <x-input-label :value="__('Descricao')" />
                                                    <x-text-input name="description" type="text" class="mt-1 block w-full" :value="$extraCost->description" required />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Tipo')" />
                                                    <select name="type" class="mt-1 block w-full">
                                                        <option value="fixed" @selected($extraCost->type === 'fixed')>Valor fixo</option>
                                                        <option value="percentage" @selected($extraCost->type === 'percentage')>Percentual</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Valor')" />
                                                    <x-text-input name="value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$extraCost->value" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Tempo de mão de obra (min)')" />
                                                    <x-text-input name="labor_minutes" type="number" step="1" min="1" class="mt-1 block w-full" :value="$extraCost->labor_minutes" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Valor da hora')" />
                                                    <x-text-input name="labor_hourly_rate" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$extraCost->labor_hourly_rate" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Salário mensal')" />
                                                    <x-text-input name="monthly_salary" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$extraCost->monthly_salary" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Horas/mês')" />
                                                    <x-text-input name="monthly_hours" type="number" step="1" min="1" class="mt-1 block w-full" :value="$extraCost->monthly_hours ?? 220" />
                                                </div>
                                                <div class="text-sm md:col-span-2" style="color: var(--pf-text-soft);">
                                                    <p class="font-semibold" style="color: var(--pf-text);">Impacto</p>
                                                    <p class="mt-1">
                                                        @if ($extraCost->type === 'percentage')
                                                            {{ number_format((float) $extraCost->value, 2, ',', '.') }}% sobre ingredientes
                                                        @else
                                                            @money((float) $extraCost->value, $recipe->company)
                                                        @endif
                                                    </p>
                                                    @if ($extraCost->labor_minutes && $extraCost->labor_hourly_rate)
                                                        <p class="mt-1">
                                                            {{ $extraCost->labor_minutes }} min x @money((float) $extraCost->labor_hourly_rate, $recipe->company) / hora
                                                        </p>
                                                    @endif
                                                    @if ($extraCost->monthly_salary)
                                                        <p class="mt-1">
                                                            Base mensal: @money((float) $extraCost->monthly_salary, $recipe->company) / {{ $extraCost->monthly_hours ?? 220 }} h
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-4 flex justify-end gap-3">
                                                <button type="submit" class="button-secondary">Salvar custo extra</button>
                                            </div>
                                        </form>
                                        <form method="POST" action="{{ route('extra-costs.destroy', $extraCost->id) }}" class="flex justify-end" onsubmit="return confirm('Deseja remover este custo extra?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button-secondary">Remover custo extra</button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>
