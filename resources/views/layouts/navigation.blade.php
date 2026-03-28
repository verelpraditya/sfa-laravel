<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-white/80 bg-white/70 backdrop-blur-xl">
    <div class="mx-auto flex h-18 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-sm font-bold text-white shadow-lg shadow-brand-600/20">
                S
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700">SFA Distributor</p>
                <p class="text-sm text-slate-500">{{ Auth::user()->roleLabel() }}</p>
            </div>
        </div>

        <div class="hidden items-center gap-3 sm:flex">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>
            <x-nav-link :href="route('outlets.index')" :active="request()->routeIs('outlets.*')">
                {{ __('Outlet') }}
            </x-nav-link>
            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                {{ __('Profil') }}
            </x-nav-link>

            <div class="ms-2 rounded-2xl border border-slate-200 bg-white px-4 py-2 shadow-sm">
                <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-500">{{ Auth::user()->username }}</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900">
                    Keluar
                </button>
            </form>
        </div>

        <button @click="open = !open" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:text-slate-900 sm:hidden">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div x-show="open" x-transition.opacity class="border-t border-slate-200 bg-white/95 px-4 py-4 shadow-xl shadow-slate-200/50 sm:hidden">
        <div class="space-y-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('outlets.index')" :active="request()->routeIs('outlets.*')">
                {{ __('Outlet') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                {{ __('Profil') }}
            </x-responsive-nav-link>
        </div>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <p class="text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
            <p class="text-xs text-slate-500">{{ Auth::user()->username }} · {{ Auth::user()->roleLabel() }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button class="inline-flex w-full items-center justify-center rounded-2xl bg-ink-950 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15">
                Keluar
            </button>
        </form>
    </div>
</nav>
