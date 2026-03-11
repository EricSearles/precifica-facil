<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Canais de venda</p>
            <!-- <h2 class="page-title">Configure marketplaces e canais próprios.</h2> -->
            <p class="page-subtitle">Cadastre iFood, balcão, WhatsApp ou qualquer outro canal e mantenha as taxas organizadas para calcular o preço final corretamente.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('sales-channels.create') }}" class="button-primary">Novo canal</a>
        </div>
    </x-slot>

    <section class="form-section">
        <div class="mb-6 flex flex-col justify-between gap-3 lg:flex-row lg:items-center">
            <div>
                <h3 class="form-section-title">Canais cadastrados</h3>
                <p class="form-section-subtitle">Cada canal pode ter taxas fixas e percentuais próprias.</p>
            </div>
            <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center">
                <form method="GET" action="{{ route('sales-channels.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar canal" class="block w-full sm:w-64">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="button-secondary">Buscar</button>
                        @if ($search !== '')
                            <a href="{{ route('sales-channels.index') }}" class="button-secondary">Limpar</a>
                        @endif
                    </div>
                </form>
                <span class="badge-neutral">{{ $salesChannels->total() }} canal(is)</span>
            </div>
        </div>

        @if ($salesChannels->isEmpty())
            <div class="empty-state">
                <p class="text-base font-semibold text-slate-900">Nenhum canal cadastrado ainda.</p>
                <p class="mt-2 text-sm text-slate-500">Comece pelo iFood ou por um canal próprio para salvar preços específicos por produto.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-[28px] border border-slate-200">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Canal</th>
                            <th>Slug</th>
                            <th>Taxas</th>
                            <th>Status</th>
                            <th class="table-actions-head">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesChannels as $salesChannel)
                            <tr>
                                <td>
                                    <p class="font-semibold text-slate-900">{{ $salesChannel->name }}</p>
                                    @if ($salesChannel->notes)
                                        <p class="mt-1 text-slate-500">{{ $salesChannel->notes }}</p>
                                    @endif
                                </td>
                                <td>{{ $salesChannel->slug }}</td>
                                <td>{{ $salesChannel->fees->count() }} taxa(s)</td>
                                <td>
                                    <span class="badge-neutral">{{ $salesChannel->is_active ? 'Ativo' : 'Inativo' }}</span>
                                </td>
                                <td class="table-actions-cell">
                                    <div class="table-actions-wrap">
                                        <a href="{{ route('sales-channels.edit', $salesChannel->id) }}" class="button-table-action">Gerenciar</a>
                                        <form method="POST" action="{{ route('sales-channels.destroy', $salesChannel->id) }}" onsubmit="return confirm('Deseja remover este canal de venda?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button-table-action">Excluir</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $salesChannels->links() }}
            </div>
        @endif
    </section>
</x-app-layout>
