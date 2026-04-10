<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <p class="text-sm font-medium text-white/70">Daftar Operasional</p>
            <h2 class="mt-1 text-2xl font-bold text-white sm:text-3xl">{{ $title }}</h2>
            <p class="mt-1 text-sm text-white/70">{{ $description }}</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Filter --}}
            <section class="app-panel app-animate-enter p-4">
                <form method="GET" class="grid gap-3 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Cari outlet" />
                        <div class="app-input-shell mt-2">
                            <span class="app-input-icon">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                            </span>
                            <x-text-input id="search" name="search" class="app-field app-field-with-icon block w-full" :value="$filters['search']" placeholder="Nama, official kode, kecamatan, kota" />
                        </div>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap gap-3">
                        <x-primary-button class="app-action-primary">Terapkan</x-primary-button>
                        <a href="{{ url()->current() }}" class="app-action-secondary">Reset</a>
                    </div>
                </form>
            </section>

            {{-- Data --}}
            <section class="app-panel app-animate-enter p-4 sm:p-5">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Daftar</p>
                        <h3 class="app-section-title mt-2">{{ $title }}</h3>
                    </div>
                    <span class="app-chip">{{ $outlets->total() }} outlet</span>
                </div>

                {{-- Desktop table --}}
                <div class="app-table-shell mt-5 hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3.5 font-semibold">Outlet</th>
                                <th class="px-4 py-3.5 font-semibold">Cabang</th>
                                @if ($variant === 'prospek')
                                    <th class="px-4 py-3.5 font-semibold">Terakhir Dikunjungi</th>
                                    <th class="px-4 py-3.5 font-semibold">User Terakhir</th>
                                @endif
                                <th class="px-4 py-3.5 font-semibold">Official Kode</th>
                                <th class="px-4 py-3.5 font-semibold">Status</th>
                                <th class="px-4 py-3.5 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($outlets as $outlet)
                                @php($lastVisit = $outlet->latestVisit)
                                @php($lastVisitedAt = $lastVisit?->visitedAtForBranch())
                                @php($daysSinceLastVisit = $lastVisitedAt ? (int) floor($lastVisitedAt->diffInDays(now($outlet->branch?->timezone ?? config('app.timezone')), true)) : null)
                                <tr class="transition duration-150 hover:bg-slate-50">
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->branch?->name }}</td>
                                    @if ($variant === 'prospek')
                                        <td class="px-4 py-4 text-slate-600">
                                            @if ($lastVisitedAt)
                                                <p>{{ $lastVisitedAt->format('d M Y H:i') }}</p>
                                                <p class="mt-1 text-xs text-slate-400">{{ $daysSinceLastVisit === 0 ? 'Hari ini' : $daysSinceLastVisit.' hari lalu' }}</p>
                                            @else
                                                <span class="text-slate-400">Belum pernah dikunjungi</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-slate-600">{{ $lastVisit?->user?->name ?? '-' }}</td>
                                    @endif
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->official_kode ?: '-' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="app-badge {{ $outlet->outlet_status === 'prospek' ? 'app-badge-violet' : ($outlet->outlet_status === 'pending' ? 'app-badge-amber' : ($outlet->outlet_status === 'active' ? 'app-badge-emerald' : 'app-badge-slate')) }}">{{ $outlet->statusLabel() }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('outlets.show', $outlet) }}" class="app-btn-sm">Detail</a>
                                            @if ($variant === 'prospek' && auth()->user()->canManageOutletMaster())
                                                <a href="{{ route('outlets.edit', $outlet) }}" class="app-btn-sm-primary">Buka Outlet</a>
                                            @elseif (auth()->user()->canVerifyOutlets())
                                                <a href="{{ route('outlet-verifications.edit', $outlet) }}" class="app-btn-sm">
                                                    Tindak Lanjut
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $variant === 'prospek' ? 7 : 5 }}" class="p-0">
                                        <div class="app-empty-state my-4 mx-4">Belum ada data untuk daftar ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile cards --}}
                <div class="mt-5 space-y-3 lg:hidden">
                    @forelse ($outlets as $outlet)
                        @php($lastVisit = $outlet->latestVisit)
                        @php($lastVisitedAt = $lastVisit?->visitedAtForBranch())
                        @php($daysSinceLastVisit = $lastVisitedAt ? (int) floor($lastVisitedAt->diffInDays(now($outlet->branch?->timezone ?? config('app.timezone')), true)) : null)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                </div>
                                <span class="app-badge shrink-0 {{ $outlet->outlet_status === 'prospek' ? 'app-badge-violet' : ($outlet->outlet_status === 'pending' ? 'app-badge-amber' : ($outlet->outlet_status === 'active' ? 'app-badge-emerald' : 'app-badge-slate')) }}">{{ $outlet->statusLabel() }}</span>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2.5 text-[13px] text-slate-600">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Cabang</p>
                                    <p class="mt-0.5 leading-5">{{ $outlet->branch?->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Official Kode</p>
                                    <p class="mt-0.5 leading-5">{{ $outlet->official_kode ?: '-' }}</p>
                                </div>
                                @if ($variant === 'prospek')
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">User Terakhir</p>
                                        <p class="mt-0.5 leading-5">{{ $lastVisit?->user?->name ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Terakhir Dikunjungi</p>
                                        @if ($lastVisitedAt)
                                            <p class="mt-0.5 leading-5">{{ $lastVisitedAt->format('d M Y H:i') }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $daysSinceLastVisit === 0 ? 'Hari ini' : $daysSinceLastVisit.' hari lalu' }}</p>
                                        @else
                                            <p class="mt-0.5 leading-5 text-slate-400">Belum pernah</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 flex items-center justify-end gap-2 border-t border-slate-100 pt-3">
                                <a href="{{ route('outlets.show', $outlet) }}" class="app-btn-sm">Detail</a>
                                @if ($variant === 'prospek' && auth()->user()->canManageOutletMaster())
                                    <a href="{{ route('outlets.edit', $outlet) }}" class="app-btn-sm-primary">Buka Outlet</a>
                                @elseif (auth()->user()->canVerifyOutlets())
                                    <a href="{{ route('outlet-verifications.edit', $outlet) }}" class="app-btn-sm">
                                        Tindak Lanjut
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada data untuk daftar ini.</div>
                    @endforelse
                </div>

                @if ($outlets->hasPages())
                    <div class="mt-5">{{ $outlets->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
