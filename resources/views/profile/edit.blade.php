<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="page-kicker">Conta</p>
            <h2 class="page-title">Perfil e seguranca</h2>
            <p class="page-subtitle">Atualize seus dados de acesso e mantenha a conta protegida.</p>
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="mx-auto max-w-5xl space-y-6">
            <div class="form-section">
                <div class="max-w-2xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="form-section">
                <div class="max-w-2xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="form-section">
                <div class="max-w-2xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>