@props([
    'packaging' => null,
    'submitLabel' => 'Salvar embalagem',
])

<div class="space-y-6">
    <div class="field-grid-2">
        <div>
            <x-input-label for="name" :value="__('Nome da embalagem')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $packaging?->name)" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="unit_cost" :value="__('Custo unitário')" />
            <x-text-input id="unit_cost" name="unit_cost" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_cost', $packaging?->unit_cost ?? 0)" required />
            <x-input-error :messages="$errors->get('unit_cost')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="notes" :value="__('Observações')" />
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full">{{ old('notes', $packaging?->notes) }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('packagings.index') }}" class="button-secondary">
            Cancelar
        </a>
        <button type="submit" class="button-primary">
            {{ $submitLabel }}
        </button>
    </div>
</div>

