@props([
    'recipe' => null,
    'products' => collect(),
    'submitLabel' => 'Salvar receita',
])

@php
    $units = ['un', 'g', 'kg', 'ml', 'l'];
@endphp

<div class="space-y-8">
    <div class="field-grid-2">
        <div>
            <x-input-label for="name" :value="__('Nome da receita')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $recipe?->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="product_id" :value="__('Produto vinculado')" />
            <select id="product_id" name="product_id" class="mt-1 block w-full">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" @selected((string) old('product_id', $recipe?->product_id) === (string) $product->id)>{{ $product->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
        </div>
    </div>

    <div class="field-grid-2">
        <div>
            <x-input-label for="yield_quantity" :value="__('Rendimento')" />
            <x-text-input id="yield_quantity" name="yield_quantity" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('yield_quantity', $recipe?->yield_quantity ?? 1)" required />
            <x-input-error :messages="$errors->get('yield_quantity')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="yield_unit" :value="__('Unidade de rendimento')" />
            <select id="yield_unit" name="yield_unit" class="mt-1 block w-full">
                @foreach ($units as $unit)
                    <option value="{{ $unit }}" @selected(old('yield_unit', $recipe?->yield_unit ?? 'un') === $unit)>{{ strtoupper($unit) }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('yield_unit')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="preparation_method" :value="__('Modo de preparo')" />
        <textarea id="preparation_method" name="preparation_method" rows="4" class="mt-1 block w-full">{{ old('preparation_method', $recipe?->preparation_method) }}</textarea>
        <x-input-error :messages="$errors->get('preparation_method')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="notes" :value="__('Observações')" />
        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full">{{ old('notes', $recipe?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('recipes.index') }}" class="button-secondary">
            Cancelar
        </a>

        <button type="submit" class="button-primary">
            {{ $submitLabel }}
        </button>
    </div>
</div>

