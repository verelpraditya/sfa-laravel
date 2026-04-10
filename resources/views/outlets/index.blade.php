<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">Master Outlet</span>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">Outlet Cabang</h2>
                    <p class="mt-1 text-sm text-white/70">Kelola outlet yang sudah terdaftar dan pantau status outlet.</p>
                </div>
                @if (auth()->user()->canManageOutletMaster())
                    <a href="{{ route('outlets.create') }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14" /></svg>
                        Tambah Outlet
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Filter Section --}}
            <section class="app-panel app-animate-enter p-4" x-data="{ filtersOpen: {{ $filters['outlet_status'] ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('outlets.index') }}">

                    {{-- Mobile: search + filter toggle --}}
                    <div class="flex flex-col gap-3 lg:hidden">
                        <div class="flex gap-2">
                            <div class="relative min-w-0 flex-1">
                                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                <input id="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari outlet..." class="app-field app-field-with-icon block w-full">
                            </div>
                            <button type="button" @click="filtersOpen = !filtersOpen" class="app-action-secondary min-h-0 px-3.5 py-2.5">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M4 6h12M6.5 10h7M8.5 14h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                                </svg>
                                Filter
                            </button>
                        </div>

                        {{-- Collapsible filter panel --}}
                        <div x-cloak x-show="filtersOpen" x-transition class="space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <div>
                                <x-input-label for="outlet_status" value="Status outlet" />
                                <select id="outlet_status" name="outlet_status" class="app-select mt-2 block w-full">
                                    <option value="">Semua</option>
                                    <option value="prospek" @selected($filters['outlet_status'] === 'prospek')>Prospek</option>
                                    <option value="pending" @selected($filters['outlet_status'] === 'pending')>Pending</option>
                                    <option value="active" @selected($filters['outlet_status'] === 'active')>Aktif</option>
                                    <option value="inactive" @selected($filters['outlet_status'] === 'inactive')>Inactive</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <x-primary-button class="flex-1">Terapkan</x-primary-button>
                                <a href="{{ route('outlets.index') }}" class="app-action-secondary flex-1 min-h-0 px-4 py-2.5 text-sm">Reset</a>
                            </div>
                        </div>
                    </div>

                    {{-- Desktop: full filters always visible --}}
                    <div class="hidden gap-3 lg:grid lg:grid-cols-4">
                        <div class="lg:col-span-2">
                            <x-input-label for="search-desktop" value="Cari outlet" />
                            <x-text-input id="search-desktop" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Nama outlet, official kode, kecamatan, kota" />
                        </div>
                        <div>
                            <x-input-label for="outlet_status-desktop" value="Status outlet" />
                            <select id="outlet_status-desktop" name="outlet_status" class="app-select mt-2 block w-full">
                                <option value="">Semua</option>
                                <option value="prospek" @selected($filters['outlet_status'] === 'prospek')>Prospek</option>
                                <option value="pending" @selected($filters['outlet_status'] === 'pending')>Pending</option>
                                <option value="active" @selected($filters['outlet_status'] === 'active')>Aktif</option>
                                <option value="inactive" @selected($filters['outlet_status'] === 'inactive')>Inactive</option>
                            </select>
                        </div>
                        <div class="lg:col-span-4 flex gap-3">
                            <x-primary-button>Terapkan Filter</x-primary-button>
                            <a href="{{ route('outlets.index') }}" class="app-action-secondary min-h-0 px-5 py-2.5 text-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </section>

            {{-- Data Section --}}
            <section class="app-panel app-animate-enter p-4 sm:p-5">

                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Daftar Outlet</p>
                        <h3 class="app-section-title mt-2">Outlet terdaftar</h3>
                    </div>
                    <span class="app-chip">{{ $outlets->total() }} outlet</span>
                </div>

                {{-- Desktop Table --}}
                <div class="app-table-shell mt-5 hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3.5 font-semibold">Outlet</th>
                                <th class="px-4 py-3.5 font-semibold">Cabang</th>
                                <th class="px-4 py-3.5 font-semibold">Official Kode</th>
                                <th class="px-4 py-3.5 font-semibold">Status</th>
                                <th class="px-4 py-3.5 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($outlets as $outlet)
                                <tr class="transition duration-150 hover:bg-slate-50">
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->branch?->name }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->official_kode ?: '-' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="app-badge {{ $outlet->outlet_status === 'active' ? 'app-badge-emerald' : ($outlet->outlet_status === 'pending' ? 'app-badge-amber' : ($outlet->outlet_status === 'prospek' ? 'app-badge-violet' : 'app-badge-rose')) }}">
                                            {{ $outlet->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('outlets.show', $outlet) }}" class="app-btn-sm-primary">Detail</a>
                                            @if (auth()->user()->canManageOutletMaster())
                                                <a href="{{ route('outlets.edit', $outlet) }}" class="app-btn-sm">Edit</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-0">
                                        <div class="app-empty-state my-4">Belum ada outlet sesuai filter saat ini.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="mt-5 space-y-3 lg:hidden">
                    @forelse ($outlets as $outlet)
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                            {{-- Card Header: name + status --}}
                            <div class="flex items-center justify-between gap-3 px-4 pt-3.5 pb-2">
                                <p class="min-w-0 truncate font-semibold text-slate-900">{{ $outlet->name }}</p>
                                <span class="app-badge shrink-0 {{ $outlet->outlet_status === 'active' ? 'app-badge-emerald' : ($outlet->outlet_status === 'pending' ? 'app-badge-amber' : ($outlet->outlet_status === 'prospek' ? 'app-badge-violet' : 'app-badge-rose')) }}">
                                    {{ $outlet->statusLabel() }}
                                </span>
                            </div>

                            {{-- Card Body: details --}}
                            <div class="grid grid-cols-2 gap-x-3 gap-y-2 px-4 pb-3 text-[13px] text-slate-600">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-slate-400">Lokasi</p>
                                    <p class="mt-0.5 leading-5">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-slate-400">Cabang</p>
                                    <p class="mt-0.5 leading-5 line-clamp-1">{{ $outlet->branch?->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-slate-400">Official Kode</p>
                                    <p class="mt-0.5 leading-5">{{ $outlet->official_kode ?: '-' }}</p>
                                </div>
                            </div>

                            {{-- Card Footer: actions --}}
                            <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-4 py-2.5">
                                @if (auth()->user()->canManageOutletMaster())
                                    <a href="{{ route('outlets.edit', $outlet) }}" class="app-btn-sm">Edit</a>
                                @endif
                                <a href="{{ route('outlets.show', $outlet) }}" class="app-btn-sm-primary">Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada outlet sesuai filter saat ini.</div>
                    @endforelse
                </div>

                @if ($outlets->hasPages())
                    <div class="mt-5">{{ $outlets->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
