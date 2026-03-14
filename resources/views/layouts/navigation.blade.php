@php
$company = Auth::user()?->company;
$accessNotice = $company?->accessNotice();
$navItems = [
['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard'],
['label' => 'Categorias', 'route' => 'categories.index', 'match' => 'categories.*'],
['label' => 'Ingredientes', 'route' => 'ingredients.index', 'match' => 'ingredients.*'],
['label' => 'Embalagens', 'route' => 'packagings.index', 'match' => 'packagings.*'],
['label' => 'Canais de venda', 'route' => 'sales-channels.index', 'match' => 'sales-channels.*'],
['label' => 'Produtos', 'route' => 'products.index', 'match' => 'products.*'],
['label' => 'Receitas', 'route' => 'recipes.index', 'match' => 'recipes.*'],
['label' => 'Meu plano', 'route' => 'billing.portal', 'match' => 'billing.*'],
['label' => 'Configurações', 'route' => 'settings.edit', 'match' => 'settings.*'],
];
@endphp

<div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" @click="sidebarOpen = false"></div>

<aside class="mobile-sidebar-shell" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="sidebar-panel">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
                <div>
                    <h1 class="mt-3 text-lg font-semibold text-white">Precifica Fácil</h1>
                    <p class="mt-1 text-sm leading-6" style="color: rgba(229, 231, 235, 0.82);">
                        Gestão simples para doceiras, confeiteiras, salgadeiras e pequenos negócios de alimentação.
                    </p>
                </div>
            </div>

            <button type="button" class="button-ghost lg:hidden" @click="sidebarOpen = false">
                Fechar
            </button>
        </div>

        <div class="mt-8 space-y-3">
            <div class="sidebar-section-label">Gestão</div>

            @foreach ($navItems as $item)
            @php
            $active = request()->routeIs($item['match']);
            @endphp
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
                <span>{{ $item['label'] }}</span>
                @if ($active)
                <span class="sidebar-pill">Ativo</span>
                @endif
            </a>
            @endforeach
        </div>

        <div class="mt-6 rounded-[24px] border p-4" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.04); color: var(--pf-sidebar-text);">
            <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color: rgba(229,231,235,0.58);">Sessão</p>
            <div class="mt-4">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-sm" style="color: rgba(229,231,235,0.78);">{{ Auth::user()->email }}</p>
            </div>

            @if ($accessNotice)
            <div class="mt-4 rounded-2xl border px-3 py-3 text-sm" style="border-color: rgba(255,255,255,0.08); background: {{ $accessNotice['blocked'] ? 'rgba(127, 29, 29, 0.32)' : 'rgba(37,99,235,0.16)' }}; color: {{ $accessNotice['blocked'] ? '#fee2e2' : '#dbeafe' }};">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em]" style="color: {{ $accessNotice['blocked'] ? 'rgba(254,226,226,0.76)' : 'rgba(219,234,254,0.76)' }};">{{ $accessNotice['label'] }}</p>
                <p class="mt-2 text-xs leading-5" style="color: {{ $accessNotice['blocked'] ? 'rgba(254,226,226,0.88)' : 'rgba(219,234,254,0.82)' }};">
                    {{ $accessNotice['message'] }}
                </p>
            </div>
            @endif

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('profile.edit') }}" class="button-secondary text-xs">
                    Perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button-ghost text-xs">
                        Sair
                    </button>
                </form>
            </div>

            <div class="mt-5 border-t pt-4 text-xs" style="border-color: rgba(255,255,255,0.08); color: rgba(229,231,235,0.72);">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('terms') }}" class="auth-link" style="color: rgba(229,231,235,0.82);">Termos de Uso</a>
                    <a href="{{ route('data-usage') }}" class="auth-link" style="color: rgba(229,231,235,0.82);">Uso de Dados</a>
                </div>
            </div>
        </div>
    </div>
</aside>

<aside class="desktop-sidebar-shell">
    <div class="sidebar-panel">
        <div class="flex items-start gap-3">
            <div>
                <div class="brand-badge">Sistema de precificação</div>
                <h1 class="mt-3 text-lg font-semibold text-white">Precifica Fácil</h1>
                <p class="mt-1 text-sm leading-6" style="color: rgba(229, 231, 235, 0.82);">
                    Gestão simples para doceiras, confeiteiras, salgadeiras e pequenos negócios de alimentação.
                </p>
            </div>
        </div>

        <div class="mt-8 space-y-3">
            <div class="sidebar-section-label">Gestão</div>

            @foreach ($navItems as $item)
            @php
            $active = request()->routeIs($item['match']);
            @endphp
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
                <span>{{ $item['label'] }}</span>
                @if ($active)
                <span class="sidebar-pill">Ativo</span>
                @endif
            </a>
            @endforeach
        </div>

        <div class="mt-6 rounded-[24px] border p-4" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.04); color: var(--pf-sidebar-text);">
            <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color: rgba(229,231,235,0.58);">Sessão</p>
            <div class="mt-4">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-sm" style="color: rgba(229,231,235,0.78);">{{ Auth::user()->email }}</p>
            </div>

            @if ($accessNotice)
            <div class="mt-4 rounded-2xl border px-3 py-3 text-sm" style="border-color: rgba(255,255,255,0.08); background: {{ $accessNotice['blocked'] ? 'rgba(127, 29, 29, 0.32)' : 'rgba(37,99,235,0.16)' }}; color: {{ $accessNotice['blocked'] ? '#fee2e2' : '#dbeafe' }};">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em]" style="color: {{ $accessNotice['blocked'] ? 'rgba(254,226,226,0.76)' : 'rgba(219,234,254,0.76)' }};">{{ $accessNotice['label'] }}</p>
                <p class="mt-2 text-xs leading-5" style="color: {{ $accessNotice['blocked'] ? 'rgba(254,226,226,0.88)' : 'rgba(219,234,254,0.82)' }};">
                    {{ $accessNotice['message'] }}
                </p>
            </div>
            @endif

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('profile.edit') }}" class="button-secondary text-xs">
                    Perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button-ghost text-xs">
                        Sair
                    </button>
                </form>
            </div>

            <div class="mt-5 border-t pt-4 text-xs" style="border-color: rgba(255,255,255,0.08); color: rgba(229,231,235,0.72);">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('terms') }}" class="auth-link" style="color: rgba(229,231,235,0.82);">Termos de Uso</a>
                    <a href="{{ route('data-usage') }}" class="auth-link" style="color: rgba(229,231,235,0.82);">Uso de Dados</a>
                </div>
            </div>
        </div>
    </div>
</aside>
