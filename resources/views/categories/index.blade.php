<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Estrutura do catálogo</p>
            <!-- <h2 class="page-title">Categorias organizadas para facilitar o cadastro.</h2> -->
            <p class="page-subtitle">Use categorias para agrupar produtos e deixar a operação mais legível para o dia a dia.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('categories.create') }}" class="button-primary">Nova categoria</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="flash-success">{{ session('success') }}</div>
        @endif

        <section class="table-shell">
            <div class="table-toolbar">
                <div>
                    <h3 class="table-title">Categorias da empresa</h3>
                    <p class="table-description">Estrutura simples para manter o catálogo de produtos limpo e consistente.</p>
                </div>
                <div class="flex flex-col items-stretch gap-3 lg:flex-row lg:items-center">
                    <form method="GET" action="{{ route('categories.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Buscar categoria" class="block w-full sm:w-64">
                        <div class="flex items-center gap-2">
                            <button type="submit" class="button-secondary">Buscar</button>
                            @if ($search !== '')
                                <a href="{{ route('categories.index') }}" class="button-secondary">Limpar</a>
                            @endif
                        </div>
                    </form>
                    <span class="badge-neutral">{{ $categories->total() }} registro(s)</span>
                </div>
            </div>

            @if ($categories->isEmpty())
                <div class="empty-state">
                    <p class="text-base font-semibold text-slate-900">Nenhuma categoria cadastrada.</p>
                    <p class="mt-2 text-sm text-slate-500">Crie categorias para organizar a linha de produtos e acelerar o cadastro.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Criada em</th>
                                <th class="table-actions-head">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td class="entity-title">{{ $category->name }}</td>
                                    <td>{{ optional($category->created_at)->format('d/m/Y H:i') }}</td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions-wrap">
                                            <a href="{{ route('categories.edit', $category->id) }}" class="button-table-action">Editar</a>
                                            <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Deseja remover esta categoria?');">
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
                    {{ $categories->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
