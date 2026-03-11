<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Estrutura da produção</p>
            <h2 class="page-title">Refine a receita e mantenha a base do cálculo consistente.</h2>
            <p class="page-subtitle">Ajuste vínculo com produto, rendimento e modo de preparo antes de revisar os itens e o custo final.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('recipes.index') }}" class="button-secondary">Voltar às receitas</a>
        </div>
    </x-slot>

    <section class="form-section max-w-5xl">
        <div class="mb-6">
            <h3 class="form-section-title">Estrutura da receita</h3>
            <p class="form-section-subtitle">Revise os dados principais antes de alterar itens, custos extras ou embalagem.</p>
        </div>

        <form method="POST" action="{{ route('recipes.update', $recipe->id) }}">
            @csrf
            @method('PUT')

            @include('recipes._form', [
                'recipe' => $recipe,
                'products' => $products,
                'submitLabel' => 'Salvar alterações',
            ])
        </form>
    </section>
</x-app-layout>
