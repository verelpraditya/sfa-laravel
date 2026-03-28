@php($user = auth()->user())

<aside class="hidden xl:flex xl:w-80 xl:flex-col xl:border-r xl:border-slate-900/10 xl:bg-[linear-gradient(180deg,#0f172a_0%,#13213b_55%,#17284a_100%)] xl:px-5 xl:py-6 xl:text-white">
    <div class="flex items-center gap-4 px-3">
        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-[radial-gradient(circle_at_top,_#38bdf8,_#2563eb_55%,_#1d4ed8_100%)] text-xl font-black text-white shadow-[0_20px_50px_-18px_rgba(56,189,248,0.9)]">S</div>
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-sky-200/70">SFA Distributor</p>
            <p class="mt-1 text-lg font-semibold text-white">{{ $user->roleLabel() }}</p>
            <p class="text-sm text-slate-300">{{ $user->branch?->name ?? 'Semua Cabang' }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-[2rem] border border-white/10 bg-white/6 px-4 py-4 shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] backdrop-blur">
        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Workspace</p>
        <p class="mt-2 text-base font-semibold text-white">{{ $user->name }}</p>
        <p class="mt-1 text-sm text-slate-300">{{ '@'.$user->username }}</p>
    </div>

    <nav class="mt-8 flex-1 space-y-2">
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
        @if ($user->isAdminPusat())
            <x-nav-link :href="route('branches.index')" :active="request()->routeIs('branches.*')">Cabang</x-nav-link>
            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">User</x-nav-link>
        @endif
        <x-nav-link :href="route('outlets.index')" :active="request()->routeIs('outlets.*')">Outlet</x-nav-link>
        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">Laporan</x-nav-link>
        @if ($user->canVerifyOutlets())
            <x-nav-link :href="route('outlet-verifications.index')" :active="request()->routeIs('outlet-verifications.*')">Verifikasi Outlet</x-nav-link>
            <x-nav-link :href="route('outlet-lists.noo')" :active="request()->routeIs('outlet-lists.noo')">Daftar NOO</x-nav-link>
            <x-nav-link :href="route('outlet-lists.inactive')" :active="request()->routeIs('outlet-lists.inactive')">Outlet Inactive</x-nav-link>
        @endif
        @if ($user->canViewOperationalOutletLists())
            <x-nav-link :href="route('outlet-lists.prospects')" :active="request()->routeIs('outlet-lists.prospects')">Prospek</x-nav-link>
        @endif
        @if ($user->canViewSalesVisitModule())
            <x-nav-link :href="route('sales-visits.index')" :active="request()->routeIs('sales-visits.*')">Kunjungan Sales</x-nav-link>
        @endif
        @if ($user->canViewSmdVisitModule())
            <x-nav-link :href="route('smd-visits.index')" :active="request()->routeIs('smd-visits.*')">Kunjungan SMD</x-nav-link>
        @endif
        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Profil</x-nav-link>
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button class="inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/8 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-sky-300/20 hover:bg-white/14 hover:text-white">
            Keluar
        </button>
    </form>
</aside>

<div x-data="{ open: false }" class="xl:hidden">
    <div class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
        <div class="flex items-center justify-between px-4 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <button @click="open = true" class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-slate-300 hover:text-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-700">SFA Distributor</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $user->roleLabel() }}</p>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white px-4 py-2 shadow-sm">
                <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                <p class="text-xs text-slate-500">{{ '@'.$user->username }}</p>
            </div>
        </div>
    </div>

    <div x-cloak x-show="open" class="fixed inset-0 z-50" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="-translate-x-full opacity-0" class="relative flex h-full w-[88%] max-w-sm flex-col bg-[linear-gradient(180deg,#0f172a_0%,#13213b_58%,#17284a_100%)] px-5 py-6 text-white shadow-2xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-[radial-gradient(circle_at_top,_#38bdf8,_#2563eb_55%,_#1d4ed8_100%)] text-lg font-black text-white shadow-[0_16px_40px_-18px_rgba(56,189,248,0.9)]">S</div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-200/70">SFA Distributor</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $user->roleLabel() }}</p>
                    </div>
                </div>
                <button @click="open = false" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/8 text-slate-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="mt-6 rounded-[1.75rem] border border-white/10 bg-white/6 px-4 py-4">
                <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                <p class="mt-1 text-xs text-slate-300">{{ '@'.$user->username }} · {{ $user->branch?->name ?? 'Semua Cabang' }}</p>
            </div>

            <nav class="mt-6 flex-1 space-y-2 overflow-y-auto pr-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
                @if ($user->isAdminPusat())
                    <x-responsive-nav-link :href="route('branches.index')" :active="request()->routeIs('branches.*')">Cabang</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">User</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('outlets.index')" :active="request()->routeIs('outlets.*')">Outlet</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">Laporan</x-responsive-nav-link>
                @if ($user->canVerifyOutlets())
                    <x-responsive-nav-link :href="route('outlet-verifications.index')" :active="request()->routeIs('outlet-verifications.*')">Verifikasi Outlet</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('outlet-lists.noo')" :active="request()->routeIs('outlet-lists.noo')">Daftar NOO</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('outlet-lists.inactive')" :active="request()->routeIs('outlet-lists.inactive')">Outlet Inactive</x-responsive-nav-link>
                @endif
                @if ($user->canViewOperationalOutletLists())
                    <x-responsive-nav-link :href="route('outlet-lists.prospects')" :active="request()->routeIs('outlet-lists.prospects')">Prospek</x-responsive-nav-link>
                @endif
                @if ($user->canViewSalesVisitModule())
                    <x-responsive-nav-link :href="route('sales-visits.index')" :active="request()->routeIs('sales-visits.*')">Kunjungan Sales</x-responsive-nav-link>
                @endif
                @if ($user->canViewSmdVisitModule())
                    <x-responsive-nav-link :href="route('smd-visits.index')" :active="request()->routeIs('smd-visits.*')">Kunjungan SMD</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">Profil</x-responsive-nav-link>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button class="inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/8 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-sky-300/20 hover:bg-white/14 hover:text-white">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>
