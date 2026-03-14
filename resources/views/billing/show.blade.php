<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Financeiro</p>
            <h2 class="page-title">Cobrança {{ $invoice->number ?: '#' . $invoice->id }}</h2>
            <p class="page-subtitle">Veja os dados da cobrança e abra o boleto, Pix ou link de pagamento sem depender da listagem.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
        <div class="flash-error">{{ session('error') }}</div>
        @endif

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="metric-card">
                <p class="metric-label">Status</p>
                <p class="metric-value">{{ $billingPortalService->formatInvoiceStatus($invoice->status) }}</p>
                <p class="metric-caption">Situação atual da cobrança.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Método</p>
                <p class="metric-value">{{ $billingPortalService->formatBillingMethod($invoice->billing_method) }}</p>
                <p class="metric-caption">Forma de pagamento escolhida.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Vencimento</p>
                <p class="metric-value">{{ $invoice->due_at?->format('d/m/Y') ?? 'Sem data' }}</p>
                <p class="metric-caption">Data prevista para pagamento.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Valor</p>
                <p class="metric-value">@money(((int) $invoice->total_cents) / 100, $company)</p>
                <p class="metric-caption">Valor total desta cobrança.</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <article class="surface-card-strong">
                <div class="flex items-start justify-between gap-4">
                    <div>
                    <p class="page-kicker">Abrir cobrança</p>
                        <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Acesso direto ao pagamento.</h3>
                    </div>
                    <span class="badge-neutral">{{ $billingPortalService->formatBillingMethod($invoice->billing_method) }}</span>
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('billing.portal') }}" class="button-secondary">Voltar para Meu plano</a>
                    @if (! $billingPortalService->invoiceHasPaymentAccess($invoice))
                    <form method="POST" action="{{ route('billing.invoices.process', $invoice) }}">
                        @csrf
                        <button type="submit" class="button-primary">Processar cobrança</button>
                    </form>
                    @endif
                    @if ($invoice->invoice_url)
                    <a href="{{ $invoice->invoice_url }}" target="_blank" rel="noreferrer" class="button-primary">Abrir fatura Asaas</a>
                    @endif
                    @if ($invoice->boleto_pdf_url)
                    <a href="{{ $invoice->boleto_pdf_url }}" target="_blank" rel="noreferrer" class="button-secondary">Baixar boleto PDF</a>
                    @endif
                </div>

                @if (! $billingPortalService->invoiceHasPaymentAccess($invoice))
                <div class="mt-8 rounded-[24px] border p-5" style="border-color: rgba(245, 158, 11, 0.28); background: rgba(245, 158, 11, 0.08);">
                    <p class="text-sm font-semibold" style="color: var(--pf-text);">A cobrança ainda não trouxe os dados de pagamento.</p>
                    <p class="mt-2 text-sm leading-6" style="color: var(--pf-text-soft);">
                        Isso costuma acontecer quando a fatura foi criada antes de concluir a comunicação com a Asaas. Use o botão <strong style="color: var(--pf-text);">Processar cobrança</strong> para tentar preencher boleto, Pix ou link de pagamento.
                    </p>
                    @if (data_get($invoice->metadata, 'last_error'))
                    <p class="mt-3 text-sm" style="color: #b45309;">Último erro: {{ data_get($invoice->metadata, 'last_error') }}</p>
                    @endif
                </div>
                @endif

                @if ($invoice->boleto_line)
                <div class="mt-8 rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                    <p class="metric-label">Linha digitável</p>
                    <p class="mt-3 break-all text-sm leading-7" style="color: var(--pf-text);">{{ $invoice->boleto_line }}</p>
                </div>
                @endif

                @if ($invoice->pix_copy_paste)
                <div class="mt-8 rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                    <p class="metric-label">Pix copia e cola</p>
                    <p class="mt-3 break-all text-sm leading-7" style="color: var(--pf-text);">{{ $invoice->pix_copy_paste }}</p>
                </div>
                @endif

                @if ($invoice->pix_qr_code)
                <div class="mt-8 rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                    <p class="metric-label">QR Code Pix</p>
                    <div class="mt-4">
                        <img src="data:image/png;base64,{{ $invoice->pix_qr_code }}" alt="QR Code Pix" class="h-64 w-64 rounded-2xl border object-contain p-3" style="border-color: var(--pf-border); background: #fff;" />
                    </div>
                </div>
                @endif
            </article>

            <article class="surface-card">
                <p class="page-kicker">Detalhes</p>
                <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Informações salvas da cobrança.</h3>

                <dl class="mt-6 space-y-4 text-sm" style="color: var(--pf-text-soft);">
                    <div class="flex items-center justify-between gap-3 rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>ID interno</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">#{{ $invoice->id }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>ID Asaas</dt>
                        <dd class="font-semibold break-all" style="color: var(--pf-text);">{{ $invoice->provider_invoice_id ?: 'Não informado' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Status Asaas</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $invoice->provider_status ?: 'Não informado' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Período</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $invoice->period_start?->format('d/m/Y') ?? '-' }} ate {{ $invoice->period_end?->format('d/m/Y') ?? '-' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Atualizada em</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $invoice->updated_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </article>
        </section>
    </div>
</x-app-layout>
