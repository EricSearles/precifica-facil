<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Base de insumos</p>
            <p class="page-subtitle">Cadastre preço, unidade de compra e unidade base para formar receitas com rapidez e menos erro operacional.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('ingredients.create') }}" class="button-primary">Novo ingrediente</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="flash-error">{{ session('error') }}</div>
        @endif

        <section class="table-shell">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Ingredientes cadastrados</h3>
                    <p class="table-description">Visão rápida do custo unitário e das informações de compra usadas na ficha técnica.</p>
                </div>
                <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center">
                    <form method="GET" action="{{ route('ingredients.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Buscar ingrediente ou marca" class="block w-full sm:w-64">
                        <select name="status" class="block w-full sm:w-44">
                            <option value="">Todos os status</option>
                            <option value="active" @selected($filters['status'] === 'active')>Somente ativos</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Somente inativos</option>
                        </select>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="button-secondary">Aplicar filtros</button>
                            @if ($filters['search'] !== '' || $filters['status'] !== '')
                                <a href="{{ route('ingredients.index') }}" class="button-secondary">Limpar</a>
                            @endif
                        </div>
                    </form>
                    <span class="badge-neutral">{{ $ingredients->total() }} registro(s)</span>
                </div>
            </div>

            @if ($ingredients->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhum ingrediente encontrado.</p>
                    <p class="mt-2 text-sm text-slate-500">Ajuste os filtros ou cadastre um novo insumo para manter suas receitas consistentes.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ingrediente</th>
                                <th>Compra</th>
                                <th>Conversão para receita</th>
                                <th>Custo unitário</th>
                                <th>Status</th>
                                <th class="table-actions-head">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ingredients as $ingredient)
                                @php
                                    $ingredientUnitCost = (float) $ingredient->unit_cost;
                                    $ingredientReferenceUnit = strtoupper($ingredient->base_unit ?: $ingredient->content_unit ?: $ingredient->purchase_unit);
                                    $ingredientUnitCostDecimals = $ingredientUnitCost > 0 && $ingredientUnitCost < 0.1 ? 4 : 2;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="entity-title">{{ $ingredient->name }}</div>
                                        <div class="entity-meta">{{ $ingredient->brand ?: 'Sem marca informada' }}</div>
                                    </td>
                                    <td>
                                        {{ $ingredient->purchase_quantity }} {{ strtoupper($ingredient->purchase_unit) }}<br>
                                        <span class="entity-meta">@money((float) $ingredient->purchase_price, $ingredient->company)</span>
                                        @if ($ingredient->content_quantity && $ingredient->content_unit)
                                            <div class="entity-meta">Conteúdo: {{ $ingredient->content_quantity }} {{ strtoupper($ingredient->content_unit) }} por {{ strtoupper($ingredient->purchase_unit) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $ingredient->base_quantity ?: '-' }} {{ strtoupper($ingredient->base_unit ?: '') }}
                                    </td>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ app(\App\Support\CompanyFormatter::class)->moneyWithDecimals($ingredientUnitCost, $ingredientUnitCostDecimals, $ingredient->company) }}</div>
                                        <div class="entity-meta">por {{ $ingredientReferenceUnit }}</div>
                                    </td>
                                    <td>
                                        <span class="{{ $ingredient->is_active ? 'badge-success' : 'badge-neutral' }}">{{ $ingredient->is_active ? 'Ativo' : 'Inativo' }}</span>
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions-wrap">
                                            <a href="{{ route('ingredients.edit', $ingredient->id) }}" class="button-table-action">Editar</a>
                                            <form method="POST" action="{{ route('ingredients.destroy', $ingredient->id) }}" onsubmit="return confirm('Deseja remover este ingrediente?');">
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
                    {{ $ingredients->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
