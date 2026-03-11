<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Base de insumos</p>
            <h2 class="page-title">Ingredientes com custo claro e prontos para composição.</h2>
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

        <section class="table-shell">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Ingredientes cadastrados</h3>
                    <p class="table-description">Visão rápida do custo unitário e das informações de compra usadas na ficha técnica.</p>
                </div>
                <span class="badge-neutral">{{ $ingredients->count() }} registro(s)</span>
            </div>

            @if ($ingredients->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhum ingrediente cadastrado.</p>
                    <p class="mt-2 text-sm text-slate-500">Cadastre os insumos principais para começar a montar receitas com custo real.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ingrediente</th>
                                <th>Compra</th>
                                <th>Unidade base</th>
                                <th>Custo unitário</th>
                                <th>Status</th>
                                <th class="table-actions-head">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ingredients as $ingredient)
                                <tr>
                                    <td>
                                        <div class="entity-title">{{ $ingredient->name }}</div>
                                        <div class="entity-meta">{{ $ingredient->brand }}</div>
                                    </td>
                                    <td>
                                        {{ $ingredient->purchase_quantity }} {{ strtoupper($ingredient->purchase_unit) }}<br>
                                        <span class="entity-meta">R$ {{ number_format((float) $ingredient->purchase_price, 2, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        {{ $ingredient->base_quantity ?: '-' }} {{ strtoupper($ingredient->base_unit ?: '') }}
                                    </td>
                                    <td class="font-semibold text-slate-900">R$ {{ number_format((float) $ingredient->unit_cost, 2, ',', '.') }}</td>
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
            @endif
        </section>
    </div>
</x-app-layout>