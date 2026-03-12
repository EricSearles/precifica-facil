<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Base de insumos</p>
            <h2 class="page-title">Ajuste os dados do ingrediente e preserve a base técnica.</h2>
            <p class="page-subtitle">Qualquer alteração aqui impacta custos de receita. Revise unidades, preço e conversão com cuidado.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('ingredients.index') }}" class="button-secondary">Voltar aos ingredientes</a>
        </div>
    </x-slot>

    <section class="form-section max-w-5xl">
        @if (session('success'))
            <div class="flash-success mb-6">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="flash-error mb-6">{{ session('error') }}</div>
        @endif

        <div class="mb-6">
            <h3 class="form-section-title">Ficha do ingrediente</h3>
            <p class="form-section-subtitle">Atualize compra, unidade base e status sem perder o histórico operacional.</p>
        </div>

        <form method="POST" action="{{ route('ingredients.update', $ingredient->id) }}">
            @csrf
            @method('PUT')

            @include('ingredients._form', [
                'ingredient' => $ingredient,
                'submitLabel' => 'Salvar alterações',
            ])
        </form>
    </section>
</x-app-layout>
