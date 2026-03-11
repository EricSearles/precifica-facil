@props([
    'product' => null,
    'categories' => collect(),
    'submitLabel' => 'Salvar produto',
])

@php
    $saleUnits = ['un', 'kg', 'g', 'ml', 'l'];
@endphp

<div class="space-y-8">
    <div class="field-grid-2">
        <div>
            <x-input-label for="name" :value="__('Nome do produto')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $product?->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="category_id" :value="__('Categoria')" />
            <select id="category_id" name="category_id" class="mt-1 block w-full">
                <option value="">Sem categoria</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) old('category_id', $product?->category_id) === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
        </div>
    </div>

    <div class="form-section bg-slate-50/70 shadow-none">
        <div class="mb-6">
            <h4 class="form-section-title">Venda e margem</h4>
            <p class="form-section-subtitle">Configure unidade, rendimento, status e regra comercial do produto.</p>
        </div>

        <div class="field-grid-3">
            <div>
                <x-input-label for="sale_unit" :value="__('Unidade de venda')" />
                <select id="sale_unit" name="sale_unit" class="mt-1 block w-full">
                    @foreach ($saleUnits as $unit)
                        <option value="{{ $unit }}" @selected(old('sale_unit', $product?->sale_unit ?? 'un') === $unit)>{{ strtoupper($unit) }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('sale_unit')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="yield_quantity" :value="__('Rendimento')" />
                <x-text-input id="yield_quantity" name="yield_quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('yield_quantity', $product?->yield_quantity ?? 1)" required />
                <x-input-error :messages="$errors->get('yield_quantity')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="is_active" :value="__('Status')" />
                <label class="mt-3 inline-flex items-center gap-3 text-sm text-slate-600">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-teal-700 shadow-sm focus:ring-teal-500" @checked(old('is_active', $product?->is_active ?? true))>
                    Produto ativo
                </label>
                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
            </div>
        </div>

        <div class="field-grid-3 mt-6">
            <div>
                <x-input-label for="profit_margin_type" :value="__('Tipo de margem')" />
                <select id="profit_margin_type" name="profit_margin_type" class="mt-1 block w-full">
                    <option value="percentage" @selected(old('profit_margin_type', $product?->profit_margin_type ?? 'percentage') === 'percentage')>Percentual</option>
                    <option value="fixed" @selected(old('profit_margin_type', $product?->profit_margin_type) === 'fixed')>Valor fixo</option>
                </select>
                <x-input-error :messages="$errors->get('profit_margin_type')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="profit_margin_value" :value="__('Valor da margem')" />
                <x-text-input id="profit_margin_value" name="profit_margin_value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('profit_margin_value', $product?->profit_margin_value ?? 0)" required />
                <x-input-error :messages="$errors->get('profit_margin_value')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="use_global_margin" :value="__('Margem global')" />
                <label class="mt-3 inline-flex items-center gap-3 text-sm text-slate-600">
                    <input type="hidden" name="use_global_margin" value="0">
                    <input type="checkbox" name="use_global_margin" value="1" class="rounded border-slate-300 text-teal-700 shadow-sm focus:ring-teal-500" @checked(old('use_global_margin', $product?->use_global_margin ?? false))>
                    Usar margem padrão da empresa
                </label>
                <x-input-error :messages="$errors->get('use_global_margin')" class="mt-2" />
            </div>
        </div>
    </div>

    <div>
        <x-input-label for="notes" :value="__('Observações')" />
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full">{{ old('notes', $product?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>

    @if ($product)
        <div class="grid gap-4 md:grid-cols-2">
            <div class="surface-card bg-slate-50/80 shadow-none">
                <p class="metric-label">Custo calculado</p>
                <p class="mt-3 text-lg font-semibold text-slate-950">@money((float) $product->calculated_unit_cost, $product->company)</p>
            </div>
            <div class="surface-card bg-slate-50/80 shadow-none">
                <p class="metric-label">Preço sugerido</p>
                <p class="mt-3 text-lg font-semibold text-slate-950">@money((float) $product->suggested_sale_price, $product->company)</p>
                @if ($product->productChannelPrices->isNotEmpty())
                    <div class="channel-price-list">
                        @foreach ($product->productChannelPrices->take(3) as $channelPrice)
                            <div class="channel-price-item">
                                <span class="channel-price-name">{{ $channelPrice->salesChannel?->name ?? 'Canal' }}</span>
                                <span class="channel-price-value">@money((float) $channelPrice->channel_price, $product->company)</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('products.index') }}" class="button-secondary">
            Cancelar
        </a>

        <button type="submit" class="button-primary">
            {{ $submitLabel }}
        </button>
    </div>
</div>
