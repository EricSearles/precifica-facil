<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="company_name" :value="__('Nome da empresa')" />
            <x-text-input id="company_name" class="mt-1 block w-full" type="text" name="company_name" :value="old('company_name')" required autofocus autocomplete="organization" />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="company_phone" :value="__('Telefone da empresa')" />
            <x-text-input id="company_phone" class="mt-1 block w-full" type="text" name="company_phone" :value="old('company_phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('company_phone')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Seu nome')" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('E-mail')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar senha')" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4 pt-2">
            <a class="auth-link" href="{{ route('login') }}">
                {{ __('Ja tem conta?') }}
            </a>

            <x-primary-button>
                {{ __('Criar conta') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>