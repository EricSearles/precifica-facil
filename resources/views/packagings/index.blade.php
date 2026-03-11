<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Custos de acondicionamento</p>
            <!-- <h2 class="page-title">Embalagens com custo unitário controlado.</h2> -->
            <p class="page-subtitle">Mantenha os materiais de venda organizados para refletir o custo real do produto no cálculo final.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('packagings.create') }}" class="button-primary">Nova embalagem</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif

        <section class="table-shell">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Cadastro de embalagens</h3>
                    <p class="table-description">Use este catálogo como base para compor o custo de embalagem por produto.</p>
                </div>
                <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center">
                    <form method="GET" action="{{ route('packagings.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar embalagem" class="block w-full sm:w-64">
                        <div class="flex items-center gap-2">
                            <button type="submit" class="button-secondary">Buscar</button>
                            @if ($search !== '')
                                <a href="{{ route('packagings.index') }}" class="button-secondary">Limpar</a>
                            @endif
                        </div>
                    </form>
                    <span class="badge-neutral">{{ $packagings->total() }} registro(s)</span>
                </div>
            </div>

            @if ($packagings->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhuma embalagem cadastrada.</p>
                    <p class="mt-2 text-sm text-slate-500">Cadastre seus materiais e depois vincule ao produto para impactar o custo da receita.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Custo unitário</th>
                                <th class="table-actions-head">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packagings as $packaging)
                                <tr>
                                    <td class="entity-title">{{ $packaging->name }}</td>
                                    <td class="font-semibold text-slate-900">@money((float) $packaging->unit_cost, $packaging->company)</td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions-wrap">
                                            <a href="{{ route('packagings.edit', $packaging->id) }}" class="button-table-action">Editar</a>
                                            <form method="POST" action="{{ route('packagings.destroy', $packaging->id) }}" onsubmit="return confirm('Deseja remover esta embalagem?');">
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
                <div class="border-t px-6 py-4" style="border-color: var(--pf-border);">
                    {{ $packagings->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
