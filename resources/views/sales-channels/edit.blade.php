<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Canal de venda</p>
            <h2 class="page-title">{{ $salesChannel->name }}</h2>
            <p class="page-subtitle">Mantenha os dados do canal e as taxas que serão aplicadas nos preços específicos dos produtos.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('sales-channels.index') }}" class="button-secondary">Voltar aos canais</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="form-section max-w-4xl">
            <div class="mb-6">
                <h3 class="form-section-title">Dados principais</h3>
                <p class="form-section-subtitle">Nome, status e observações do canal.</p>
            </div>

            <form method="POST" action="{{ route('sales-channels.update', $salesChannel->id) }}">
                @csrf
                @method('PUT')
                @include('sales-channels._form', ['salesChannel' => $salesChannel, 'submitLabel' => 'Salvar alterações'])
            </form>
        </section>

        <section class="form-section">
            <div class="mb-6 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="form-section-title">Taxas do canal</h3>
                    <p class="form-section-subtitle">Cadastre comissões percentuais e cobranças fixas. Toda alteração atualiza os preços já salvos nos produtos.</p>
                </div>
                <span class="badge-neutral">{{ $salesChannel->fees->count() }} taxa(s)</span>
            </div>

            <form method="POST" action="{{ route('sales-channel-fees.store') }}" class="grid gap-4 md:grid-cols-5">
                @csrf
                <input type="hidden" name="sales_channel_id" value="{{ $salesChannel->id }}">

                <div>
                    <x-input-label for="fee_name" :value="__('Nome da taxa')" />
                    <x-text-input id="fee_name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="fee_type" :value="__('Tipo')" />
                    <select id="fee_type" name="type" class="mt-1 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                        <option value="percentage" @selected(old('type') === 'percentage')>Percentual</option>
                        <option value="fixed" @selected(old('type') === 'fixed')>Fixa</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="fee_value" :value="__('Valor')" />
                    <x-text-input id="fee_value" name="value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('value')" required />
                    <x-input-error :messages="$errors->get('value')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="fee_active" :value="__('Status')" />
                    <label class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        <input id="fee_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true))>
                        Taxa ativa
                    </label>
                </div>

                <div class="flex items-end justify-end">
                    <button type="submit" class="button-primary">Adicionar taxa</button>
                </div>
            </form>

            @if ($salesChannel->fees->isEmpty())
                <div class="empty-state mt-6">
                    <p class="text-base font-semibold text-slate-900">Nenhuma taxa cadastrada ainda.</p>
                    <p class="mt-2 text-sm text-slate-500">Você pode combinar taxas percentuais e fixas para reproduzir o cálculo real do marketplace.</p>
                </div>
            @else
                <div class="mt-6 space-y-4">
                    @foreach ($salesChannel->fees as $fee)
                        <div class="rounded-[24px] border border-slate-200 bg-slate-50/70 p-5">
                            <form method="POST" action="{{ route('sales-channel-fees.update', $fee->id) }}" class="grid gap-4 md:grid-cols-5">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label :value="__('Nome da taxa')" />
                                    <x-text-input name="name" type="text" class="mt-1 block w-full" :value="$fee->name" required />
                                </div>

                                <div>
                                    <x-input-label :value="__('Tipo')" />
                                    <select name="type" class="mt-1 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                                        <option value="percentage" @selected($fee->type === 'percentage')>Percentual</option>
                                        <option value="fixed" @selected($fee->type === 'fixed')>Fixa</option>
                                    </select>
                                </div>

                                <div>
                                    <x-input-label :value="__('Valor')" />
                                    <x-text-input name="value" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="$fee->value" required />
                                </div>

                                <div>
                                    <x-input-label :value="__('Status')" />
                                    <label class="mt-3 flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">
                                        <input name="is_active" type="checkbox" value="1" @checked($fee->is_active)>
                                        Taxa ativa
                                    </label>
                                </div>

                                <div class="flex items-end justify-end gap-2">
                                    <button type="submit" class="button-secondary">Salvar</button>
                                </div>
                            </form>

                            <div class="mt-4 flex items-center justify-between border-t border-slate-200 pt-4 text-sm text-slate-500">
                                <span>{{ $fee->type === 'percentage' ? 'Incide sobre o preço do canal' : 'Valor fixo por venda/pedido' }}</span>
                                <form method="POST" action="{{ route('sales-channel-fees.destroy', $fee->id) }}" onsubmit="return confirm('Deseja remover esta taxa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button-secondary">Remover</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</x-app-layout>