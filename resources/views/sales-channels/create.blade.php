<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Novo canal</p>
            <h2 class="page-title">Cadastre um novo canal de venda.</h2>
            <p class="page-subtitle">Crie a estrutura do canal primeiro e depois adicione as taxas fixas e percentuais na tela seguinte.</p>
        </div>
    </x-slot>

    <section class="form-section max-w-4xl">
        <div class="mb-6">
            <h3 class="form-section-title">Dados do canal</h3>
            <p class="form-section-subtitle">Use nomes claros para facilitar a escolha no produto, como iFood Entrega, iFood Balcão ou WhatsApp.</p>
        </div>

        <form method="POST" action="{{ route('sales-channels.store') }}">
            @csrf
            @include('sales-channels._form', ['submitLabel' => 'Salvar canal'])
        </form>
    </section>
</x-app-layout>