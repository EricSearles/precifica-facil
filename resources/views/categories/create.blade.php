<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Estrutura do catálogo</p>
            <h2 class="page-title">Nova categoria para organizar seus produtos.</h2>
            <p class="page-subtitle">Crie grupos claros para manter o cadastro limpo e facilitar a seleção nas telas de produto.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('categories.index') }}" class="button-secondary">Voltar às categorias</a>
        </div>
    </x-slot>

    <section class="form-section max-w-3xl">
        <div class="mb-6">
            <h3 class="form-section-title">Dados da categoria</h3>
            <p class="form-section-subtitle">Use um nome curto e fácil de reconhecer no dia a dia.</p>
        </div>

        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

            @include('categories._form', [
                'submitLabel' => 'Criar categoria',
            ])
        </form>
    </section>
</x-app-layout>
