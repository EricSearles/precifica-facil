<section>
    <header>
        <h2 class="form-section-title">
            Informações da conta e cobrança
        </h2>

        <p class="form-section-subtitle">
            Atualize seus dados de acesso e os dados da empresa usados para faturamento na Asaas.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="'Seu nome'" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="'Seu e-mail'" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl border px-4 py-3" style="border-color: var(--pf-border); background: rgba(245, 158, 11, 0.08);">
                    <p class="text-sm" style="color: var(--pf-text);">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="auth-link ml-1">{{ __('Click here to re-send the verification email.') }}</button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm" style="color: var(--pf-success);">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="rounded-[24px] border p-5" style="border-color: var(--pf-border); background: #f8fbff;">
            <p class="text-sm font-semibold" style="color: var(--pf-text);">Dados da empresa para cobrança</p>
            <p class="mt-2 text-sm leading-6" style="color: var(--pf-text-soft);">
                Esses campos são usados para gerar boleto, Pix e link de pagamento. O celular com DDD é obrigatório para a Asaas.
            </p>
        </div>

        <div>
            <x-input-label for="company_name" :value="'Nome da empresa'" />
            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name', $user->company?->name)" required autocomplete="organization" />
            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
        </div>

        <div>
            <x-input-label for="company_email" :value="'E-mail da empresa'" />
            <x-text-input id="company_email" name="company_email" type="email" class="mt-1 block w-full" :value="old('company_email', $user->company?->email ?? $user->email)" required autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('company_email')" />
        </div>

        <div>
            <x-input-label for="company_document" :value="'CPF ou CNPJ da empresa'" />
            <x-text-input id="company_document" name="company_document" type="text" class="mt-1 block w-full" :value="old('company_document', $user->company?->document)" required autocomplete="off" placeholder="Somente números ou com pontuação" />
            <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Obrigatório para emissão de boleto e outras cobranças na Asaas.</p>
            <x-input-error class="mt-2" :messages="$errors->get('company_document')" />
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <x-input-label for="company_phone" :value="'Telefone da empresa'" />
                <x-text-input id="company_phone" name="company_phone" type="text" class="mt-1 block w-full" :value="old('company_phone', $user->company?->phone)" autocomplete="tel-national" placeholder="1133334444" />
                <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Opcional, mas se preenchido precisa estar com DDD.</p>
                <x-input-error class="mt-2" :messages="$errors->get('company_phone')" />
            </div>

            <div>
                <x-input-label for="company_mobile_phone" :value="'Celular da empresa'" />
                <x-text-input id="company_mobile_phone" name="company_mobile_phone" type="text" class="mt-1 block w-full" :value="old('company_mobile_phone', $user->company?->mobile_phone)" required autocomplete="tel" placeholder="11999998888" />
                <p class="mt-2 text-xs" style="color: var(--pf-text-soft);">Obrigatório para gerar cobrança na Asaas.</p>
                <x-input-error class="mt-2" :messages="$errors->get('company_mobile_phone')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Salvar</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm" style="color: var(--pf-text-soft);">Dados salvos.</p>
            @endif
        </div>
    </form>
</section>
