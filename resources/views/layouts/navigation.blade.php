@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard'],
        ['label' => 'Categorias', 'route' => 'categories.index', 'match' => 'categories.*'],
        ['label' => 'Ingredientes', 'route' => 'ingredients.index', 'match' => 'ingredients.*'],
        ['label' => 'Embalagens', 'route' => 'packagings.index', 'match' => 'packagings.*'],
        ['label' => 'Canais de venda', 'route' => 'sales-channels.index', 'match' => 'sales-channels.*'],
        ['label' => 'Produtos', 'route' => 'products.index', 'match' => 'products.*'],
        ['label' => 'Receitas', 'route' => 'recipes.index', 'match' => 'recipes.*'],
        ['label' => 'Configurações', 'route' => 'settings.edit', 'match' => 'settings.*'],
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
                    <div class="brand-badge">SaaS de precificação</div>
                    <h1 class="mt-3 text-xl font-semibold text-white">Precifica Fácil</h1>
                    <p class="mt-1 text-sm leading-6 text-slate-300">
                        Gestão simples para doceiras, confeiteiras, salgadeiras e pequenos negócios de alimentação.
                    </p>
                </div>
            </div>

            <button type="button" class="button-ghost text-slate-300 lg:hidden" @click="sidebarOpen = false">
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

        <div class="mt-auto rounded-[24px] border border-white/10 bg-white/5 p-4 text-slate-200">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Sessão</p>
            <div class="mt-4">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-sm text-slate-300">{{ Auth::user()->email }}</p>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('profile.edit') }}" class="button-secondary text-xs">
                    Perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button-ghost text-xs text-slate-200">
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
                <div class="brand-badge">SaaS de precificação</div>
                <h1 class="mt-3 text-xl font-semibold text-white">Precifica Fácil</h1>
                <p class="mt-1 text-sm leading-6 text-slate-300">
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

        <div class="mt-auto rounded-[24px] border border-white/10 bg-white/5 p-4 text-slate-200">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Sessão</p>
            <div class="mt-4">
                <p class="text-sm font-semibold text-white">{{ Auth::user()->name }}</p>
                <p class="mt-1 text-sm text-slate-300">{{ Auth::user()->email }}</p>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                <a href="{{ route('profile.edit') }}" class="button-secondary text-xs">
                    Perfil
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="button-ghost text-xs text-slate-200">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>