<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Financeiro</p>
            <h2 class="page-title">Meu plano</h2>
            <p class="page-subtitle">Acompanhe plano ativo, situação da assinatura e faturas da empresa em um único lugar.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
        <div class="flash-error">{{ session('error') }}</div>
        @endif

        @if (! $summary['setup_ready'])
        <section class="surface-card-strong">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="page-kicker">Implantação</p>
                    <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Billing preparado no código, aguardando migrations.</h3>
                    <p class="mt-3 max-w-3xl text-sm leading-6" style="color: var(--pf-text-soft);">
                        A navegação já está pronta e a estrutura de cobrança foi adicionada. Para ativar as consultas de faturas e pagamentos, rode as migrations ao final.
                    </p>
                </div>
                <span class="badge-neutral">Pendente</span>
            </div>
        </section>
        @endif

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <article class="metric-card">
                <p class="metric-label">Plano atual</p>
                <p class="metric-value">{{ $resolved_plan?->name ?? $subscription?->plan?->name ?? $summary['plan_name'] }}</p>
                <p class="metric-caption">Base usada para licenciamento da empresa.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Situação da empresa</p>
                <p class="metric-value">{{ $summary['company_status'] }}</p>
                <p class="metric-caption">Status geral da conta no sistema.</p>
            </article>

            <article class="metric-card">
                <p class="metric-label">Assinatura</p>
                <p class="metric-value">{{ app(\App\Services\Billing\BillingPortalService::class)->formatSubscriptionStatus($subscription?->status) }}</p>
                <p class="metric-caption">Ciclo e cobrança recorrente do plano.</p>
            </article>
        </section>

        <section class="surface-card-strong">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="page-kicker">Planos</p>
                    <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Escolha o plano ideal ou faça upgrade.</h3>
                    <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">A troca de plano já está pronta. As travas por limite continuam desativadas nesta fase.</p>
                </div>
                <span class="badge-neutral">Upgrade pronto</span>
            </div>

            <div class="mt-8 grid gap-4 xl:grid-cols-3">
                @foreach ([
                    ['code' => 'starter', 'name' => 'Plano Iniciante', 'price' => 'R$ 14,90/mês', 'summary' => 'Para quem está começando com produção pequena.', 'limits' => '20 ingredientes, 15 produtos, 15 receitas, 1 usuário'],
                    ['code' => 'professional', 'name' => 'Plano Profissional', 'price' => 'R$ 25,90/mês', 'summary' => 'Melhor opção para operação regular e marketplaces.', 'limits' => '200 ingredientes, 200 produtos, 200 receitas, 3 usuários'],
                    ['code' => 'business', 'name' => 'Plano Negócio', 'price' => 'R$ 79,90/mês', 'summary' => 'Para equipe, volume maior e gestão avançada.', 'limits' => 'Ingredientes, produtos e receitas ilimitados, até 10 usuários'],
                ] as $planOption)
                @php
                $isCurrentPlan = ($resolved_plan?->code ?? auth()->user()->company?->plan) === $planOption['code'];
                @endphp
                <article class="rounded-[24px] border p-5" style="border-color: {{ $isCurrentPlan ? 'rgba(37,99,235,0.28)' : 'var(--pf-border)' }}; background: #fff;">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--pf-text);">{{ $planOption['name'] }}</p>
                            <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">{{ $planOption['price'] }}</p>
                        </div>
                        <span class="{{ $isCurrentPlan ? 'badge-success' : 'badge-neutral' }}">{{ $isCurrentPlan ? 'Atual' : 'Disponível' }}</span>
                    </div>

                    <p class="mt-4 text-sm leading-6" style="color: var(--pf-text-soft);">{{ $planOption['summary'] }}</p>
                    <p class="mt-3 text-sm" style="color: var(--pf-text-soft);">{{ $planOption['limits'] }}</p>

                    <form method="POST" action="{{ route('billing.change-plan') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $planOption['code'] }}">
                        <button type="submit" class="{{ $isCurrentPlan ? 'button-secondary' : 'button-primary' }}" @disabled($isCurrentPlan)>
                            {{ $isCurrentPlan ? 'Plano atual' : 'Escolher plano' }}
                        </button>
                    </form>
                </article>
                @endforeach
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <article class="surface-card-strong">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Plano e cobrança</p>
                        <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Visão consolidada do financeiro.</h3>
                    </div>
                    @if ($next_invoice)
                    <span class="badge-accent">Próxima fatura</span>
                    @else
                    <span class="badge-neutral">Sem faturas abertas</span>
                    @endif
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <form method="POST" action="{{ route('billing.prepare') }}">
                        @csrf
                        <input type="hidden" name="method" value="{{ $subscription?->billing_method ?? 'boleto' }}">
                        <button type="submit" class="button-secondary">Preparar cobrança</button>
                    </form>

                    <form method="POST" action="{{ route('billing.charge') }}">
                        @csrf
                        <input type="hidden" name="method" value="boleto">
                        <button type="submit" class="button-primary">Gerar boleto</button>
                    </form>

                    <form method="POST" action="{{ route('billing.charge') }}">
                        @csrf
                        <input type="hidden" name="method" value="pix">
                        <button type="submit" class="button-secondary">Gerar Pix</button>
                    </form>

                    <form method="POST" action="{{ route('billing.charge') }}">
                        @csrf
                        <input type="hidden" name="method" value="card">
                        <button type="submit" class="button-secondary">Gerar link cartão</button>
                    </form>
                </div>

                <dl class="mt-8 space-y-4 text-sm" style="color: var(--pf-text-soft);">
                    <div class="flex items-center justify-between gap-3 rounded-[20px] border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Cliente de cobrança</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $customer?->name ?? auth()->user()->company?->name ?? 'Não definido' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-[20px] border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Email de cobrança</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $customer?->billing_email ?? $customer?->email ?? auth()->user()->company?->email ?? auth()->user()->email }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-[20px] border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>CPF/CNPJ</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $customer?->document ?? auth()->user()->company?->document ?? 'Não informado' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-[20px] border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Método principal</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $subscription?->billing_method ? app(\App\Services\Billing\BillingPortalService::class)->formatBillingMethod($subscription->billing_method) : 'Não definido' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-[20px] border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Próximo vencimento</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">{{ $next_invoice?->due_at?->format('d/m/Y') ?? $subscription?->next_due_at?->format('d/m/Y') ?? 'Sem data' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-[20px] border p-4" style="border-color: var(--pf-border); background: #fff;">
                        <dt>Valor esperado</dt>
                        <dd class="font-semibold" style="color: var(--pf-text);">
                            @if ($next_invoice)
                            @money(((int) $next_invoice->total_cents) / 100, auth()->user()->company)
                            @elseif ($subscription)
                            @money(((int) $subscription->amount_cents) / 100, auth()->user()->company)
                            @else
                            R$ 0,00
                            @endif
                        </dd>
                    </div>
                </dl>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <article class="surface-card-strong">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Estrutura do plano</p>
                        <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Limites e recursos preparados.</h3>
                        <p class="mt-3 text-sm leading-6" style="color: var(--pf-text-soft);">As travas ainda não estão ativas no sistema. Esta camada foi deixada pronta para a próxima etapa.</p>
                    </div>
                    <span class="badge-neutral">Sem bloqueio</span>
                </div>

                @php
                $limits = data_get($resolved_plan, 'metadata.limits', []);
                $highlights = data_get($resolved_plan, 'metadata.highlights', []);
                @endphp

                @if (! $resolved_plan)
                <div class="empty-state mt-8">
                    Nenhum plano estruturado encontrado para a empresa atual.
                </div>
                @else
                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Ingredientes</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">{{ is_null($limits['ingredients'] ?? null) ? 'Ilimitado' : $limits['ingredients'] }}</p>
                    </div>
                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Produtos</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">{{ is_null($limits['products'] ?? null) ? 'Ilimitado' : $limits['products'] }}</p>
                    </div>
                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Receitas</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">{{ is_null($limits['recipes'] ?? null) ? 'Ilimitado' : $limits['recipes'] }}</p>
                    </div>
                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <p class="metric-label">Usuários</p>
                        <p class="mt-2 text-2xl font-semibold" style="color: var(--pf-text);">{{ is_null($limits['users'] ?? null) ? 'Ilimitado' : $limits['users'] }}</p>
                    </div>
                </div>

                @if (! empty($highlights))
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($highlights as $highlight)
                    <span class="badge-neutral">{{ $highlight }}</span>
                    @endforeach
                </div>
                @endif
                @endif
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-2">
            <article class="surface-card-strong">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Faturas em aberto</p>
                        <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Pendências e próximos vencimentos.</h3>
                    </div>
                    <span class="badge-neutral">{{ $open_invoices->count() }} item(ns)</span>
                </div>

                @if ($open_invoices->isEmpty())
                <div class="empty-state mt-8">
                    Nenhuma fatura em aberto encontrada para esta empresa.
                </div>
                @else
                <div class="mt-8 space-y-4">
                    @foreach ($open_invoices as $invoice)
                    <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #fff;">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--pf-text);">{{ $invoice->number ?: 'Fatura #' . $invoice->id }}</p>
                                <p class="mt-1 text-sm" style="color: var(--pf-text-soft);">Vencimento em {{ $invoice->due_at?->format('d/m/Y') ?? 'sem data' }}</p>
                            </div>
                            <span class="badge-neutral">{{ app(\App\Services\Billing\BillingPortalService::class)->formatInvoiceStatus($invoice->status) }}</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                            <span style="color: var(--pf-text-soft);">{{ app(\App\Services\Billing\BillingPortalService::class)->formatBillingMethod($invoice->billing_method) }}</span>
                            <strong style="color: var(--pf-text);">@money(((int) $invoice->total_cents) / 100, auth()->user()->company)</strong>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <a href="{{ route('billing.invoices.show', $invoice) }}" class="button-primary text-xs">Ver cobrança</a>
                            @if ($invoice->invoice_url)
                            <a href="{{ $invoice->invoice_url }}" target="_blank" rel="noreferrer" class="button-secondary text-xs">Abrir link</a>
                            @endif
                            @if ($invoice->boleto_pdf_url)
                            <a href="{{ $invoice->boleto_pdf_url }}" target="_blank" rel="noreferrer" class="button-secondary text-xs">Boleto PDF</a>
                            @endif
                        </div>
                        @if ($invoice->pix_copy_paste)
                        <div class="mt-4 rounded-2xl border p-3 text-xs" style="border-color: var(--pf-border); background: #f8fbff; color: var(--pf-text-soft);">
                            <strong style="color: var(--pf-text);">Pix copia e cola:</strong>
                            <div class="mt-2 break-all">{{ $invoice->pix_copy_paste }}</div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </article>

            <article class="surface-card">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="page-kicker">Histórico</p>
                        <h3 class="mt-2 text-lg font-semibold" style="color: var(--pf-text);">Faturas recentes.</h3>
                    </div>
                    <span class="badge-neutral">{{ $recent_invoices->count() }} registro(s)</span>
                </div>

                @if ($recent_invoices->isEmpty())
                <div class="empty-state mt-8">
                    Assim que as cobranças forem geradas, o histórico aparecerá aqui.
                </div>
                @else
                <div class="mt-8 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr style="color: var(--pf-text-soft);">
                                <th class="px-4 py-3 text-left font-semibold">Fatura</th>
                                <th class="px-4 py-3 text-left font-semibold">Vencimento</th>
                                <th class="px-4 py-3 text-left font-semibold">Método</th>
                                <th class="px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-4 py-3 text-right font-semibold">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recent_invoices as $invoice)
                            <tr class="border-t" style="border-color: var(--pf-border); color: var(--pf-text);">
                                <td class="px-4 py-3">{{ $invoice->number ?: 'Fatura #' . $invoice->id }}</td>
                                <td class="px-4 py-3">{{ $invoice->due_at?->format('d/m/Y') ?? '-' }}</td>
                                <td class="px-4 py-3">{{ app(\App\Services\Billing\BillingPortalService::class)->formatBillingMethod($invoice->billing_method) }}</td>
                                <td class="px-4 py-3">{{ app(\App\Services\Billing\BillingPortalService::class)->formatInvoiceStatus($invoice->status) }}</td>
                                <td class="px-4 py-3 text-right">@money(((int) $invoice->total_cents) / 100, auth()->user()->company)</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </article>
        </section>
    </div>
</x-app-layout>
