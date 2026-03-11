<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Configuração da empresa</p>
            <!-- <h2 class="page-title">Defina a base global da precificação.</h2> -->
            <p class="page-subtitle">Essas configurações ajudam a manter o padrão comercial da empresa e servem de referência para os próximos ajustes do cálculo.</p>
        </div>
    </x-slot>

    <section class="form-section max-w-4xl">
        <div class="mb-6">
            <h3 class="form-section-title">Preferências gerais</h3>
            <p class="form-section-subtitle">Margem padrão, moeda e casas decimais usadas pela empresa.</p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="field-grid-3">
                <div>
                    <x-input-label for="default_profit_margin" :value="__('Margem padrão (%)')" />
                    <x-text-input id="default_profit_margin" name="default_profit_margin" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('default_profit_margin', $setting->default_profit_margin)" required />
                    <x-input-error :messages="$errors->get('default_profit_margin')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="currency" :value="__('Moeda')" />
                    <select id="currency" name="currency" class="mt-1 block w-full">
                        <option value="BRL" @selected(old('currency', $setting->currency) === 'BRL')>BRL</option>
                        <option value="USD" @selected(old('currency', $setting->currency) === 'USD')>USD</option>
                        <option value="EUR" @selected(old('currency', $setting->currency) === 'EUR')>EUR</option>
                    </select>
                    <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="decimal_places" :value="__('Casas decimais')" />
                    <select id="decimal_places" name="decimal_places" class="mt-1 block w-full">
                        @foreach ([0, 1, 2, 3, 4] as $decimalPlace)
                            <option value="{{ $decimalPlace }}" @selected((int) old('decimal_places', $setting->decimal_places) === $decimalPlace)>{{ $decimalPlace }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('decimal_places')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                <button type="submit" class="button-primary">Salvar configurações</button>
            </div>
        </form>
    </section>
</x-app-layout>
