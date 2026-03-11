@props([
    'ingredient' => null,
    'submitLabel' => 'Salvar ingrediente',
])

@php
    $units = ['un', 'g', 'kg', 'ml', 'l'];
@endphp

<div class="space-y-8">
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
            <p class="form-section-subtitle">Esses dados definem o cálculo do custo unitário do ingrediente.</p>
        </div>

        <div class="field-grid-3">
            <div>
                <x-input-label for="purchase_unit" :value="__('Unidade de compra')" />
                <select id="purchase_unit" name="purchase_unit" class="mt-1 block w-full">
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}" @selected(old('purchase_unit', $ingredient?->purchase_unit) === $unit)>{{ strtoupper($unit) }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('purchase_unit')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="purchase_quantity" :value="__('Quantidade comprada')" />
                <x-text-input id="purchase_quantity" name="purchase_quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('purchase_quantity', $ingredient?->purchase_quantity)" required />
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
                <x-input-label for="base_unit" :value="__('Unidade base')" />
                <select id="base_unit" name="base_unit" class="mt-1 block w-full">
                    <option value="">Selecione</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}" @selected(old('base_unit', $ingredient?->base_unit) === $unit)>{{ strtoupper($unit) }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('base_unit')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="base_quantity" :value="__('Quantidade base')" />
                <x-text-input id="base_quantity" name="base_quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('base_quantity', $ingredient?->base_quantity)" />
                <x-input-error :messages="$errors->get('base_quantity')" class="mt-2" />
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

    <div>
        <x-input-label for="notes" :value="__('Observações')" />
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full">{{ old('notes', $ingredient?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>

    @if ($ingredient)
        <div class="badge-neutral w-fit">
            Custo unitário atual: R$ {{ number_format((float) $ingredient->unit_cost, 2, ',', '.') }}
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


