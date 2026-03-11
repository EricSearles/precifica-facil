<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Operação e entrega</p>
            <h2 class="page-title">Atualize a embalagem sem perder o padrão de custo.</h2>
            <p class="page-subtitle">Revise valores e descrição para manter o custo de entrega coerente com o produto final.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('packagings.index') }}" class="button-secondary">Voltar às embalagens</a>
        </div>
    </x-slot>

    <section class="form-section max-w-4xl">
        <div class="mb-6">
            <h3 class="form-section-title">Ficha da embalagem</h3>
            <p class="form-section-subtitle">Ajuste o custo unitário e as observações operacionais da embalagem.</p>
        </div>

        <form method="POST" action="{{ route('packagings.update', $packaging->id) }}">
            @csrf
            @method('PUT')
            @include('packagings._form', ['packaging' => $packaging, 'submitLabel' => 'Salvar alterações'])
        </form>
    </section>
</x-app-layout>
