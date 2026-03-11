<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Operação e entrega</p>
            <h2 class="page-title">Cadastre uma embalagem para compor o custo final.</h2>
            <p class="page-subtitle">Use essa base para refletir copos, potes, caixas e demais itens de entrega no custo dos produtos.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('packagings.index') }}" class="button-secondary">Voltar às embalagens</a>
        </div>
    </x-slot>

    <section class="form-section max-w-4xl">
        <div class="mb-6">
            <h3 class="form-section-title">Ficha da embalagem</h3>
            <p class="form-section-subtitle">Registre o nome, custo unitário e qualquer observação útil para o time.</p>
        </div>

        <form method="POST" action="{{ route('packagings.store') }}">
            @csrf
            @include('packagings._form', ['submitLabel' => 'Criar embalagem'])
        </form>
    </section>
</x-app-layout>
