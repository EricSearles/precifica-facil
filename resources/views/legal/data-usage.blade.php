<x-guest-layout>
    <div class="space-y-6">
        <div>
            <p class="page-kicker">Uso de dados</p>
            <h2 class="page-title">Como tratamos os dados informados na plataforma</h2>
            <p class="page-subtitle">Este documento resume como os dados são utilizados dentro do Precifica Fácil.</p>
        </div>

        <div class="space-y-5 text-sm leading-7" style="color: var(--pf-text-soft);">
            <section>
                <h3 class="form-section-title">1. Dados coletados</h3>
                <p class="mt-2">Podemos armazenar dados cadastrais da empresa, dados do usuário responsável, informações operacionais lançadas na plataforma e registros necessários para funcionamento, segurança e suporte do sistema.</p>
            </section>

            <section>
                <h3 class="form-section-title">2. Finalidade do uso</h3>
                <p class="mt-2">Os dados são utilizados para criar e manter a conta, permitir acesso à plataforma, calcular custos e preços, organizar receitas, canais de venda e embalagens, além de viabilizar suporte técnico e evolução do produto.</p>
            </section>

            <section>
                <h3 class="form-section-title">3. Dados lançados pelo usuário</h3>
                <p class="mt-2">Ingredientes, receitas, preços, margens, custos, taxas e demais informações inseridas no sistema permanecem vinculadas à empresa cadastrada e são usadas para apresentar relatórios, cálculos e rotinas operacionais dentro da aplicação.</p>
            </section>

            <section>
                <h3 class="form-section-title">4. Compartilhamento</h3>
                <p class="mt-2">Os dados não são compartilhados com terceiros para fins comerciais dentro deste documento. Eventuais serviços de infraestrutura, autenticação, hospedagem e envio de e-mail podem tratar dados estritamente para viabilizar a operação da plataforma.</p>
            </section>

            <section>
                <h3 class="form-section-title">5. Segurança e retenção</h3>
                <p class="mt-2">São adotadas medidas razoáveis de segurança compatíveis com a operação da plataforma. Os dados poderão ser mantidos pelo tempo necessário para funcionamento do serviço, cumprimento de obrigações e histórico operacional da conta.</p>
            </section>

            <section>
                <h3 class="form-section-title">6. Aceite</h3>
                <p class="mt-2">Ao criar a conta e utilizar o Precifica Fácil, o usuário declara ciência deste documento e concorda com o uso dos dados conforme descrito nesta página e nos Termos de Uso.</p>
            </section>
        </div>

        <div class="flex flex-wrap gap-3 pt-2">
            <a href="{{ route('register') }}" class="button-primary">Voltar ao cadastro</a>
            <a href="{{ route('terms') }}" class="button-secondary">Ver Termos de Uso</a>
        </div>
    </div>
</x-guest-layout>
