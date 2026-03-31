@php
    $user = auth()->user();

    $navItems = [];

    $navItems[] = ['label' => 'Dashboard', 'route' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'dashboard'];

    if ($user->isAdminPusat()) {
        $navItems[] = ['label' => 'Cabang', 'route' => route('branches.index'), 'active' => request()->routeIs('branches.*'), 'icon' => 'branch'];
        $navItems[] = ['label' => 'User', 'route' => route('users.index'), 'active' => request()->routeIs('users.*'), 'icon' => 'users'];
    }

    $navItems[] = ['label' => 'Outlet', 'route' => route('outlets.index'), 'active' => request()->routeIs('outlets.*'), 'icon' => 'outlet'];
    $navItems[] = ['label' => 'History Kunjungan', 'route' => route('visit-history.index'), 'active' => request()->routeIs('visit-history.*'), 'icon' => 'history'];

    if ($user->canViewReports()) {
        $navItems[] = ['label' => 'Laporan', 'route' => route('reports.index'), 'active' => request()->routeIs('reports.*'), 'icon' => 'report'];
    }

    if ($user->canVerifyOutlets()) {
        $navItems[] = ['label' => 'Verifikasi Outlet', 'route' => route('outlet-verifications.index'), 'active' => request()->routeIs('outlet-verifications.*'), 'icon' => 'shield'];
        $navItems[] = ['label' => 'Outlet Inactive', 'route' => route('outlet-lists.inactive'), 'active' => request()->routeIs('outlet-lists.inactive'), 'icon' => 'pause'];
    }

    if ($user->canViewOperationalOutletLists()) {
        $navItems[] = ['label' => 'Prospek', 'route' => route('outlet-lists.prospects'), 'active' => request()->routeIs('outlet-lists.prospects'), 'icon' => 'target'];
    }

    if ($user->canViewSalesVisitModule()) {
        $navItems[] = ['label' => 'Kunjungan Sales', 'route' => route('sales-visits.index'), 'active' => request()->routeIs('sales-visits.*'), 'icon' => 'sales'];
    }

    if ($user->canViewSmdVisitModule()) {
        $navItems[] = ['label' => 'Kunjungan SMD', 'route' => route('smd-visits.index'), 'active' => request()->routeIs('smd-visits.*'), 'icon' => 'activity'];
    }

    $navItems[] = ['label' => 'Profil', 'route' => route('profile.edit'), 'active' => request()->routeIs('profile.*'), 'icon' => 'profile'];

    $navIcon = static function (string $icon): string {
        return match ($icon) {
            'dashboard' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.75 12.75 12 4.75l7.25 8v6a1.5 1.5 0 0 1-1.5 1.5H6.25a1.5 1.5 0 0 1-1.5-1.5v-6Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 20.25v-5.5h4.5v5.5" /></svg>',
            'branch' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.75 19.25h14.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.5 19.25v-9.5h4.25v9.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.25 19.25V5.75h4.25v13.5" /></svg>',
            'users' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.25 18.25v-1a3.25 3.25 0 0 0-3.25-3.25H8.25A3.25 3.25 0 0 0 5 17.25v1" /><circle cx="10.125" cy="8.25" r="3.25" stroke-width="1.75" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.25 18.25v-.5a2.75 2.75 0 0 0-2.75-2.75h-.75" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.75 5.5a2.75 2.75 0 0 1 0 5.5" /></svg>',
            'outlet' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5.5 10.25h13" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.25 10.25V7.5l1.5-2.75h8.5l1.5 2.75v2.75" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.25 10.25v8.5h11.5v-8.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 18.75v-4.5h6v4.5" /></svg>',
            'history' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.75 12a7.25 7.25 0 1 0 2.124-5.126" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.75 5.75v4h4" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8.5v4l2.75 1.75" /></svg>',
            'report' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.75 18.25V10.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 18.25V5.75" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17.25 18.25v-6" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.75 18.25h14.5" /></svg>',
            'shield' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.75c2.9 1.72 5.442 2.5 7.25 2.5v4.306c0 4.42-2.41 7.77-7.25 9.694-4.84-1.924-7.25-5.273-7.25-9.694V7.25c1.808 0 4.35-.78 7.25-2.5Z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m9.5 12.25 1.75 1.75 3.25-3.5" /></svg>',
            'spark' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m12 4.75 1.9 3.85 4.35.625-3.125 3.046.737 4.304L12 14.545 8.138 16.58l.737-4.304L5.75 9.225 10.1 8.6 12 4.75Z" /></svg>',
            'pause' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="6.25" y="5.25" width="4" height="13.5" rx="1.25" stroke-width="1.75" /><rect x="13.75" y="5.25" width="4" height="13.5" rx="1.25" stroke-width="1.75" /></svg>',
            'target' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="7.25" stroke-width="1.75" /><circle cx="12" cy="12" r="3.25" stroke-width="1.75" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 2.75v2.5M12 18.75v2.5M21.25 12h-2.5M5.25 12h-2.5" /></svg>',
            'sales' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5.75 17.25 10 13l2.75 2.75L18.25 9.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.75 9.5h3.5V13" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5.75 5.75v12.5h12.5" /></svg>',
            'activity' => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4.75 12h4l2.25-5 4 10 2.25-5h2" /></svg>',
            default => '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8.25" r="3.25" stroke-width="1.75" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.5 18.25a5.5 5.5 0 0 1 11 0" /></svg>',
        };
    };
@endphp

<aside class="hidden xl:flex xl:w-[22rem] xl:flex-col xl:pl-4 xl:pr-3 xl:py-4">
    <div class="relative flex h-full flex-col overflow-hidden rounded-[2.15rem] border border-white/12 bg-[radial-gradient(circle_at_top_left,rgba(103,232,249,0.22),transparent_20%),linear-gradient(180deg,#091122_0%,#0f172a_18%,#13213b_62%,#17284a_100%)] px-5 py-6 text-white shadow-[0_36px_90px_-42px_rgba(2,6,23,0.95)]">
        <div class="app-grid-glow absolute inset-0 opacity-[0.08]"></div>
        <div class="absolute -right-12 top-8 h-32 w-32 rounded-full bg-sky-400/18 blur-3xl"></div>
        <div class="absolute -left-12 bottom-20 h-40 w-40 rounded-full bg-cyan-300/10 blur-3xl"></div>

        <div class="relative flex items-center gap-4 px-1">
            <div class="app-animate-float flex h-14 w-14 items-center justify-center rounded-[1.6rem] bg-[radial-gradient(circle_at_top,_#67e8f9,_#38bdf8_35%,_#2563eb_75%,_#1d4ed8_100%)] text-xl font-black text-white shadow-[0_20px_50px_-20px_rgba(56,189,248,0.95)]">S</div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-sky-200/70">SFA Distributor</p>
                <p class="mt-1 text-lg font-semibold text-white">{{ $user->roleLabel() }}</p>
                <p class="text-sm text-slate-300">{{ $user->branch?->name ?? 'Semua Cabang' }}</p>
            </div>
        </div>

        <div class="relative mt-8 overflow-hidden rounded-[1.9rem] border border-white/10 bg-white/8 p-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur">
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Workspace</p>
                    <p class="mt-2 text-base font-semibold text-white">{{ $user->name }}</p>
                    <p class="mt-1 text-sm text-slate-300">{{ '@'.$user->username }}</p>
                </div>
                <span class="inline-flex items-center rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-200">
                    {{ $user->is_active ? 'Online' : 'Paused' }}
                </span>
            </div>
        </div>

        <div class="relative mt-8 flex items-center justify-between px-1">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Navigation</p>
            <span class="text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-200/70">{{ now()->translatedFormat('D, d M') }}</span>
        </div>

        <nav class="relative mt-4 flex-1 space-y-2 overflow-y-auto pr-1">
            @foreach ($navItems as $item)
                <x-nav-link :href="$item['route']" :active="$item['active']">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/6 text-slate-100 transition duration-200 group-hover:border-white/16 group-hover:bg-white/12">
                        {!! $navIcon($item['icon']) !!}
                    </span>
                    <span class="flex-1">{{ $item['label'] }}</span>
                    <span class="h-2 w-2 rounded-full {{ $item['active'] ? 'bg-cyan-300 shadow-[0_0_18px_rgba(103,232,249,0.9)]' : 'bg-white/10 group-hover:bg-white/20' }}"></span>
                </x-nav-link>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="relative mt-6">
            @csrf
            <button class="inline-flex w-full items-center justify-center rounded-[1.35rem] border border-white/12 bg-white/10 px-4 py-3 text-sm font-semibold text-slate-100 transition duration-200 hover:-translate-y-0.5 hover:border-sky-300/20 hover:bg-white/14 hover:text-white">
                Keluar
            </button>
        </form>
    </div>
</aside>

<div x-data="{ open: false }" class="xl:hidden">
    <div class="sticky top-0 z-40 border-b border-white/70 bg-white/82 shadow-[0_20px_40px_-34px_rgba(15,23,42,0.35)] backdrop-blur-xl">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <button @click="open = true" class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-[1.15rem] border border-slate-200/90 bg-white text-slate-700 shadow-[0_12px_30px_-20px_rgba(15,23,42,0.3)] transition hover:border-slate-300 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-700">SFA Distributor</p>
                        <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $user->roleLabel() }} · {{ $user->branch?->name ?? 'Semua Cabang' }}</p>
                    </div>
                </div>
                <div class="rounded-[1.2rem] border border-white/70 bg-white/86 px-3 py-2 text-right shadow-[0_12px_30px_-22px_rgba(15,23,42,0.25)] backdrop-blur">
                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="text-[11px] uppercase tracking-[0.18em] text-slate-500">{{ now()->translatedFormat('d M') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50" role="dialog" aria-modal="true" @keydown.escape.window="open = false">
        <div class="absolute inset-0 bg-slate-950/48 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-250" x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-full opacity-0" class="relative flex h-full w-[90%] max-w-sm flex-col overflow-hidden bg-[radial-gradient(circle_at_top_left,rgba(103,232,249,0.22),transparent_20%),linear-gradient(180deg,#091122_0%,#0f172a_18%,#13213b_62%,#17284a_100%)] px-5 py-6 text-white shadow-2xl">
            <div class="app-grid-glow absolute inset-0 opacity-[0.08]"></div>
            <div class="absolute -right-16 top-10 h-36 w-36 rounded-full bg-sky-400/20 blur-3xl"></div>

            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-[1.4rem] bg-[radial-gradient(circle_at_top,_#67e8f9,_#38bdf8_35%,_#2563eb_75%,_#1d4ed8_100%)] text-lg font-black text-white shadow-[0_16px_40px_-18px_rgba(56,189,248,0.9)]">S</div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-200/70">SFA Distributor</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $user->roleLabel() }}</p>
                    </div>
                </div>
                <button @click="open = false" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/8 text-slate-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="relative mt-6 rounded-[1.75rem] border border-white/10 bg-white/7 p-4">
                <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                <p class="mt-1 text-xs text-slate-300">{{ '@'.$user->username }} · {{ $user->branch?->name ?? 'Semua Cabang' }}</p>
                <div class="mt-4 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-200">
                    <span class="app-status-dot bg-emerald-400 shadow-[0_0_16px_rgba(74,222,128,0.8)]"></span>
                    Workspace aktif
                </div>
            </div>

            <nav class="relative mt-6 flex-1 space-y-2 overflow-y-auto pr-1">
                @foreach ($navItems as $item)
                    <x-responsive-nav-link :href="$item['route']" :active="$item['active']">
                        <span class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/6 text-slate-100 transition duration-200 group-hover:border-white/16 group-hover:bg-white/12">
                                {!! $navIcon($item['icon']) !!}
                            </span>
                            <span class="flex-1">{{ $item['label'] }}</span>
                            <span class="h-2 w-2 rounded-full {{ $item['active'] ? 'bg-cyan-300 shadow-[0_0_18px_rgba(103,232,249,0.9)]' : 'bg-white/10 group-hover:bg-white/20' }}"></span>
                        </span>
                    </x-responsive-nav-link>
                @endforeach
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="relative mt-6">
                @csrf
                <button class="inline-flex w-full items-center justify-center rounded-[1.3rem] border border-white/10 bg-white/10 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-sky-300/20 hover:bg-white/14 hover:text-white">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>
