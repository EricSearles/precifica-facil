<div class="space-y-8">
    <div class="field-grid-2">
        <div>
            <x-input-label for="name" :value="__('Nome do canal')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $salesChannel->name ?? '')" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="is_active" :value="__('Status')" />
            <label class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $salesChannel->is_active ?? true))>
                Canal ativo para cálculo e uso nos produtos.
            </label>
            <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="notes" :value="__('Observações')" />
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-emerald-500">{{ old('notes', $salesChannel->notes ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
        <a href="{{ route('sales-channels.index') }}" class="button-secondary">Cancelar</a>
        <button type="submit" class="button-primary">{{ $submitLabel }}</button>
    </div>
</div>