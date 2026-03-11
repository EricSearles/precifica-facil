@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard'],
        ['label' => 'Categorias', 'route' => 'categories.index', 'match' => 'categories.*'],
        ['label' => 'Ingredientes', 'route' => 'ingredients.index', 'match' => 'ingredients.*'],
        ['label' => 'Embalagens', 'route' => 'packagings.index', 'match' => 'packagings.*'],
        ['label' => 'Canais de venda', 'route' => 'sales-channels.index', 'match' => 'sales-channels.*'],
        ['label' => 'Produtos', 'route' => 'products.index', 'match' => 'products.*'],
        ['label' => 'Receitas', 'route' => 'recipes.index', 'match' => 'recipes.*'],
        ['label' => 'Configuracoes', 'route' => 'settings.edit', 'match' => 'settings.*'],
    ];
@endphp

<div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" @click="sidebarOpen = false"></div>

<aside class="mobile-sidebar-shell" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="sidebar-panel">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
                <span class="brand-mark">
                    <x-application-logo class="h-6 w-6 fill-current text-white" />
                </span>

                <div>
                    <div class="brand-badge">SaaS de precificacao</div>
                    <h1 class="mt-3 text-xl font-semibold text-white">Precifica Facil</h1>
                    <p class="mt-1 text-sm leading-6" style="color: rgba(229, 231, 235, 0.82);">
                        Gestao simples para doceiras, confeiteiras, salgadeiras e pequenos negocios de alimentacao.
                    </p>
                </div>
            </div>

            <button type="button" class="button-ghost lg:hidden" @click="sidebarOpen = false">
                Fechar
            </button>
        </div>

        <div class="mt-8 space-y-3">
            <div class="sidebar-section-label">Gestao</div>

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

        <div class="mt-auto rounded-[24px] border p-4" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.04); color: var(--pf-sidebar-text);">
            <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color: rgba(229,231,235,0.58);">Sessao</p>
            <div class="mt-4">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-sm" style="color: rgba(229,231,235,0.78);">{{ Auth::user()->email }}</p>
            </div>

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
        </div>
    </div>
</aside>

<aside class="desktop-sidebar-shell">
    <div class="sidebar-panel">
        <div class="flex items-start gap-3">
            <span class="brand-mark">
                <x-application-logo class="h-6 w-6 fill-current text-white" />
            </span>

            <div>
                <div class="brand-badge">SaaS de precificacao</div>
                <h1 class="mt-3 text-xl font-semibold text-white">Precifica Facil</h1>
                <p class="mt-1 text-sm leading-6" style="color: rgba(229, 231, 235, 0.82);">
                    Gestao simples para doceiras, confeiteiras, salgadeiras e pequenos negocios de alimentacao.
                </p>
            </div>
        </div>

        <div class="mt-8 space-y-3">
            <div class="sidebar-section-label">Gestao</div>

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

        <div class="mt-auto rounded-[24px] border p-4" style="border-color: rgba(255,255,255,0.08); background: rgba(255,255,255,0.04); color: var(--pf-sidebar-text);">
            <p class="text-xs font-semibold uppercase tracking-[0.22em]" style="color: rgba(229,231,235,0.58);">Sessao</p>
            <div class="mt-4">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-sm" style="color: rgba(229,231,235,0.78);">{{ Auth::user()->email }}</p>
            </div>

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
        </div>
    </div>
</aside>