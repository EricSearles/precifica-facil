@props([
    'category' => null,
    'submitLabel' => 'Salvar categoria',
])

<div class="space-y-6">
    <div>
        <x-input-label for="name" :value="__('Nome da categoria')" />
        <x-text-input
            id="name"
            name="name"
            type="text"
            class="mt-1 block w-full"
            :value="old('name', $category?->name)"
            required
            autofocus
        />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('categories.index') }}" class="button-secondary">
            Cancelar
        </a>

        <button type="submit" class="button-primary">
            {{ $submitLabel }}
        </button>
    </div>
</div>
