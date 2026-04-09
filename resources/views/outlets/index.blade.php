<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="app-overline">Master Outlet</p>
                <h2 class="app-page-title mt-2">Outlet Cabang</h2>
                <p class="app-body-copy mt-2 max-w-3xl">Kelola outlet yang sudah terdaftar, pantau status outlet, dan siapkan data untuk autocomplete kunjungan tanpa reload.</p>
            </div>
            @if (auth()->user()->canManageOutletMaster())
                <a href="{{ route('outlets.create') }}" class="app-action-primary">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14" /></svg>
                    Tambah Outlet
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="app-alert app-alert-success">
                    <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/80 text-emerald-600 shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="m5 13 4 4L19 7" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[12px] font-semibold uppercase tracking-[0.14em] text-emerald-700">Sukses</p>
                        <p class="mt-1 font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <section class="app-panel p-4 sm:p-5" x-data="{ mobileFiltersOpen: {{ $filters['outlet_status'] ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('outlets.index') }}" class="space-y-3">
                    <div class="flex flex-col gap-3 sm:hidden">
                        <div class="flex gap-2">
                            <div class="relative min-w-0 flex-1">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                <input id="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari outlet..." class="app-field app-field-with-icon block w-full">
                            </div>
                            <button type="button" @click="mobileFiltersOpen = !mobileFiltersOpen" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] transition hover:border-slate-300 hover:bg-slate-50">
                                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M4 6h12M6.5 10h7M8.5 14h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                                </svg>
                                Filter
                            </button>
                            <x-primary-button class="px-4" type="submit">Cari</x-primary-button>
                        </div>

                        <div x-cloak x-show="mobileFiltersOpen" x-transition class="space-y-3 rounded-[1.35rem] border border-slate-200 bg-slate-50 p-3">
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
                            <a href="{{ route('outlets.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset Filter</a>
                        </div>
                    </div>

                    <div class="hidden gap-3 sm:grid sm:grid-cols-2 xl:grid-cols-4">
                        <div class="sm:col-span-2 xl:col-span-2">
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
                        <div class="sm:col-span-2 xl:col-span-4 flex gap-3">
                            <x-primary-button class="flex-1 sm:flex-none">Terapkan Filter</x-primary-button>
                            <a href="{{ route('outlets.index') }}" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100 sm:flex-none sm:px-5">Reset</a>
                        </div>
                    </div>
                </form>
            </section>

            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <div class="app-table-shell hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Cabang</th>
                                <th class="px-4 py-3 font-semibold">Official Kode</th>
                                <th class="px-4 py-3 font-semibold">Status Outlet</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($outlets as $outlet)
                                <tr>
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->branch?->name }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->official_kode ?: '-' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->outlet_status === 'prospek' ? 'bg-violet-50 text-violet-700' : ($outlet->outlet_status === 'pending' ? 'bg-amber-50 text-amber-700' : ($outlet->outlet_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700')) }}">
                                            {{ $outlet->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if (auth()->user()->canManageOutletMaster())
                                            <a href="{{ route('outlets.edit', $outlet) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">Edit</a>
                                        @else
                                            <span class="text-xs font-semibold text-slate-400">View only</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada outlet sesuai filter saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($outlets as $outlet)
                        <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 p-3.5 shadow-[0_14px_28px_-24px_rgba(15,23,42,0.35)]">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->outlet_status === 'prospek' ? 'bg-violet-50 text-violet-700' : ($outlet->outlet_status === 'pending' ? 'bg-amber-50 text-amber-700' : ($outlet->outlet_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600')) }}">{{ $outlet->statusLabel() }}</span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2.5 text-[13px] text-slate-600">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Cabang</p>
                                    <p class="mt-0.5 leading-5 line-clamp-2">{{ $outlet->branch?->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Status</p>
                                    <p class="mt-0.5 leading-5">{{ $outlet->statusLabel() }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Official Kode</p>
                                    <p class="mt-0.5 leading-5">{{ $outlet->official_kode ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-200 pt-2.5">
                                <span class="text-[11px] font-medium text-slate-400">{{ $outlet->creator?->name ? 'Dibuat oleh '.$outlet->creator->name : 'Data outlet' }}</span>
                                @if (auth()->user()->canManageOutletMaster())
                                    <a href="{{ route('outlets.edit', $outlet) }}" class="inline-flex items-center rounded-xl border border-sky-200 bg-white px-3.5 py-2 text-xs font-semibold text-sky-900 shadow-sm shadow-sky-100/50">Edit</a>
                                @else
                                    <span class="text-xs font-semibold text-slate-400">View only</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada outlet sesuai filter saat ini.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $outlets->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
