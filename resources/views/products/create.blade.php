<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Gestão comercial</p>
            <h2 class="page-title">Crie um produto pronto para precificação.</h2>
            <p class="page-subtitle">Defina categoria, unidade de venda, rendimento e regra de margem para começar a formação de preço corretamente.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('products.index') }}" class="button-secondary">Voltar aos produtos</a>
        </div>
    </x-slot>

    <section class="form-section max-w-5xl">
        <div class="mb-6">
            <h3 class="form-section-title">Dados principais do produto</h3>
            <p class="form-section-subtitle">Essas definições orientam o cálculo comercial e o vínculo futuro com a receita.</p>
        </div>

        <form method="POST" action="{{ route('products.store') }}">
            @csrf

            @include('products._form', [
                'categories' => $categories,
                'submitLabel' => 'Criar produto',
            ])
        </form>
    </section>
</x-app-layout>
