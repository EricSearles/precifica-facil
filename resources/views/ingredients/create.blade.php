<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Base de insumos</p>
            <h2 class="page-title">Cadastre um ingrediente com custo e unidade corretos.</h2>
            <p class="page-subtitle">Essa é a base do cálculo técnico. Preencha compra, conversão e status para o custo unitário sair consistente.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('ingredients.index') }}" class="button-secondary">Voltar aos ingredientes</a>
        </div>
    </x-slot>

    <section class="form-section max-w-5xl">
        <div class="mb-6">
            <h3 class="form-section-title">Ficha do ingrediente</h3>
            <p class="form-section-subtitle">Informe unidade de compra, unidade base e preço para automatizar o custo unitário.</p>
        </div>

        <form method="POST" action="{{ route('ingredients.store') }}">
            @csrf

            @include('ingredients._form', [
                'submitLabel' => 'Criar ingrediente',
            ])
        </form>
    </section>
</x-app-layout>
