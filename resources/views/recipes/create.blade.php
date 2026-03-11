<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Estrutura da produção</p>
            <h2 class="page-title">Monte uma receita vinculada ao produto certo.</h2>
            <p class="page-subtitle">Defina rendimento, unidade e modo de preparo para preparar a composição que será usada no cálculo.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('recipes.index') }}" class="button-secondary">Voltar às receitas</a>
        </div>
    </x-slot>

    <section class="form-section max-w-5xl">
        <div class="mb-6">
            <h3 class="form-section-title">Estrutura da receita</h3>
            <p class="form-section-subtitle">Depois de salvar, você poderá adicionar ingredientes, custos extras e embalagens.</p>
        </div>

        <form method="POST" action="{{ route('recipes.store') }}">
            @csrf

            @include('recipes._form', [
                'products' => $products,
                'submitLabel' => 'Criar receita',
            ])
        </form>
    </section>
</x-app-layout>
