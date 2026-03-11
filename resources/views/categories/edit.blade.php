<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Estrutura do catálogo</p>
            <h2 class="page-title">Edite a categoria sem perder o padrão do catálogo.</h2>
            <p class="page-subtitle">Ajuste nomenclatura e mantenha os agrupamentos consistentes para facilitar o cadastro dos produtos.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('categories.index') }}" class="button-secondary">Voltar às categorias</a>
        </div>
    </x-slot>

    <section class="form-section max-w-3xl">
        <div class="mb-6">
            <h3 class="form-section-title">Dados da categoria</h3>
            <p class="form-section-subtitle">Revise o nome usado nas listagens e formulários internos.</p>
        </div>

        <form method="POST" action="{{ route('categories.update', $category->id) }}">
            @csrf
            @method('PUT')

            @include('categories._form', [
                'category' => $category,
                'submitLabel' => 'Salvar alterações',
            ])
        </form>
    </section>
</x-app-layout>
