@props([
    'ingredient' => null,
    'submitLabel' => 'Salvar ingrediente',
])

@php
    $purchaseUnits = ['un', 'cx', 'lata', 'pacote', 'garrafa', 'pote', 'saco', 'g', 'kg', 'ml', 'l'];
    $measureUnits = ['un', 'g', 'kg', 'ml', 'l'];
@endphp

<div
    class="space-y-8"
    x-data="ingredientConversionForm({
        purchaseUnit: @js(old('purchase_unit', $ingredient?->purchase_unit)),
        purchaseQuantity: @js(old('purchase_quantity', $ingredient?->purchase_quantity)),
        contentUnit: @js(old('content_unit', $ingredient?->content_unit)),
        contentQuantity: @js(old('content_quantity', $ingredient?->content_quantity)),
        baseUnit: @js(old('base_unit', $ingredient?->base_unit)),
        baseQuantity: @js(old('base_quantity', $ingredient?->base_quantity)),
    })"
>
    <div class="field-grid-2">
        <div>
            <x-input-label for="name" :value="__('Nome do ingrediente')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $ingredient?->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="brand" :value="__('Marca')" />
            <x-text-input id="brand" name="brand" type="text" class="mt-1 block w-full" :value="old('brand', $ingredient?->brand)" />
            <x-input-error :messages="$errors->get('brand')" class="mt-2" />
        </div>
    </div>

    <div class="form-section bg-slate-50/70 shadow-none">
        <div class="mb-6">
            <h4 class="form-section-title">Compra e conversão</h4>
            <p class="form-section-subtitle">Informe como o ingrediente é comprado e, quando houver embalagem, qual medida existe dentro dela.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white/70 p-5">
            <div class="mb-4">
                <h5 class="text-sm font-semibold" style="color: var(--pf-text);">Como eu compro</h5>
                <p class="mt-1 text-xs" style="color: var(--pf-text-soft);">Informe como esse item é comprado e qual o valor pago.</p>
            </div>

            <div class="field-grid-3">
                <div>
                    <x-input-label for="purchase_unit" :value="__('Unidade de compra')" />
                    <select id="purchase_unit" name="purchase_unit" class="mt-1 block w-full" x-model="purchaseUnit">
                        @foreach ($purchaseUnits as $unit)
                            <option value="{{ $unit }}" @selected(old('purchase_unit', $ingredient?->purchase_unit) === $unit)>{{ strtoupper($unit) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('purchase_unit')" class="mt-2" />
                    <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Ex.: UN, CX, LATA, PACOTE, GARRAFA, KG ou L.</p>
                </div>

                <div>
                    <x-input-label for="purchase_quantity" :value="__('Quantidade comprada')" />
                    <x-text-input id="purchase_quantity" name="purchase_quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('purchase_quantity', $ingredient?->purchase_quantity)" x-model="purchaseQuantity" required />
                    <x-input-error :messages="$errors->get('purchase_quantity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="purchase_price" :value="__('Preço de compra')" />
                    <x-text-input id="purchase_price" name="purchase_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('purchase_price', $ingredient?->purchase_price)" required />
                    <x-input-error :messages="$errors->get('purchase_price')" class="mt-2" />
                </div>
            </div>

            <div class="field-grid-3 mt-6">
                <div>
                    <x-input-label for="content_quantity" :value="__('Medida da embalagem')" />
                    <x-text-input id="content_quantity" name="content_quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('content_quantity', $ingredient?->content_quantity)" x-model="contentQuantity" />
                    <x-input-error :messages="$errors->get('content_quantity')" class="mt-2" />
                    <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Ex.: 395 para uma lata de leite condensado ou 1 para uma caixa de leite.</p>
                </div>

                <div>
                    <x-input-label for="content_unit" :value="__('Unidade da embalagem')" />
                    <select id="content_unit" name="content_unit" class="mt-1 block w-full" x-model="contentUnit">
                        <option value="">Selecione</option>
                        @foreach ($measureUnits as $unit)
                            <option value="{{ $unit }}" @selected(old('content_unit', $ingredient?->content_unit) === $unit)>{{ strtoupper($unit) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('content_unit')" class="mt-2" />
                    <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Ex.: G, KG, ML, L ou UN.</p>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-3xl border border-slate-200 bg-white/70 p-5">
            <div class="mb-4">
                <h5 class="text-sm font-semibold" style="color: var(--pf-text);">Como eu uso na receita</h5>
                <p class="mt-1 text-xs" style="color: var(--pf-text-soft);">Defina a medida usada na ficha técnica e deixe o sistema fazer a equivalência.</p>
            </div>

            <div class="field-grid-3">
                <div>
                    <x-input-label for="base_unit" :value="__('Unidade usada na receita')" />
                    <select id="base_unit" name="base_unit" class="mt-1 block w-full" x-model="baseUnit">
                        <option value="">Selecione</option>
                        @foreach ($measureUnits as $unit)
                            <option value="{{ $unit }}" @selected(old('base_unit', $ingredient?->base_unit) === $unit)>{{ strtoupper($unit) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('base_unit')" class="mt-2" />
                    <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Use a medida que você realmente lança na receita.</p>
                </div>

                <div>
                    <x-input-label for="base_quantity" :value="__('Quantidade equivalente')" />
                    <input type="hidden" name="base_quantity" x-bind:value="baseQuantity">
                    <input id="base_quantity" type="text" class="mt-1 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 shadow-sm" x-bind:value="baseQuantity" readonly>
                    <x-input-error :messages="$errors->get('base_quantity')" class="mt-2" />
                    <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Calculado automaticamente com base na compra, embalagem e medida usada na receita.</p>
                    <p class="mt-2 text-xs font-medium" style="color: var(--pf-primary);" x-show="purchaseUnit && baseUnit && baseQuantity" x-text="conversionHint()"></p>
                    <p class="mt-2 text-xs" style="color: var(--pf-danger);" x-show="needsContentDefinition() && !baseQuantity">Para esse tipo de compra, informe a medida da embalagem e a unidade da embalagem.</p>
                </div>

                <div>
                    <x-input-label for="is_active" :value="__('Status')" />
                    <label class="mt-3 inline-flex items-center gap-3 text-sm text-slate-600">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-teal-700 shadow-sm focus:ring-teal-500" @checked(old('is_active', $ingredient?->is_active ?? true))>
                        Ingrediente ativo
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    <div>
        <x-input-label for="notes" :value="__('Observações')" />
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full">{{ old('notes', $ingredient?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>

    @if ($ingredient)
        @php
            $ingredientUnitCost = (float) $ingredient->unit_cost;
            $ingredientReferenceUnit = strtoupper($ingredient->base_unit ?: $ingredient->content_unit ?: $ingredient->purchase_unit);
            $ingredientUnitCostDecimals = $ingredientUnitCost > 0 && $ingredientUnitCost < 0.1 ? 4 : 2;
        @endphp
        <div class="badge-neutral w-fit">
            Custo unitário atual: {{ app(\App\Support\CompanyFormatter::class)->moneyWithDecimals($ingredientUnitCost, $ingredientUnitCostDecimals, $ingredient->company) }} por {{ $ingredientReferenceUnit }}
        </div>
    @endif

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('ingredients.index') }}" class="button-secondary">
            Cancelar
        </a>

        <button type="submit" class="button-primary">
            {{ $submitLabel }}
        </button>
    </div>
</div>


