<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Master Outlet</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Outlet Cabang</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">Kelola outlet yang sudah terdaftar, cek status verifikasi, dan siapkan data untuk autocomplete kunjungan tanpa reload.</p>
            </div>
            @if (auth()->user()->canManageOutletMaster())
                <a href="{{ route('outlets.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] px-5 py-3 text-sm font-semibold text-white shadow-[0_20px_40px_-18px_rgba(29,78,216,0.75)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_46px_-18px_rgba(29,78,216,0.9)]">
                    Tambah Outlet
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="app-panel p-4 sm:p-5" x-data="{ mobileFiltersOpen: {{ $filters['status'] || $filters['type'] || $filters['outlet_status'] ? 'true' : 'false' }} }">
                <form method="GET" action="{{ route('outlets.index') }}" class="space-y-3">
                    <div class="flex flex-col gap-3 sm:hidden">
                        <div class="flex gap-2">
                            <div class="relative min-w-0 flex-1">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" />
                                </svg>
                                <input id="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari outlet..." class="block w-full rounded-2xl border border-slate-200/90 bg-white py-3 pl-11 pr-4 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
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
                                <x-input-label for="status" value="Verifikasi" />
                                <select id="status" name="status" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                    <option value="">Semua</option>
                                    <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                                    <option value="verified" @selected($filters['status'] === 'verified')>Verified</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="type" value="Jenis" />
                                    <select id="type" name="type" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                        <option value="">Semua</option>
                                        <option value="prospek" @selected($filters['type'] === 'prospek')>Prospek</option>
                                        <option value="noo" @selected($filters['type'] === 'noo')>NOO</option>
                                        <option value="pelanggan_lama" @selected($filters['type'] === 'pelanggan_lama')>Pelanggan Lama</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="outlet_status" value="Status" />
                                    <select id="outlet_status" name="outlet_status" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                        <option value="">Semua</option>
                                        <option value="active" @selected($filters['outlet_status'] === 'active')>Active</option>
                                        <option value="inactive" @selected($filters['outlet_status'] === 'inactive')>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <a href="{{ route('outlets.index') }}" class="inline-flex w-full items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset Filter</a>
                        </div>
                    </div>

                    <div class="hidden gap-3 sm:grid sm:grid-cols-2 xl:grid-cols-5">
                        <div class="sm:col-span-2 xl:col-span-2">
                            <x-input-label for="search-desktop" value="Cari outlet" />
                            <x-text-input id="search-desktop" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Nama outlet, official kode, kecamatan, kota" />
                        </div>
                        <div>
                            <x-input-label for="status-desktop" value="Verifikasi" />
                            <select id="status-desktop" name="status" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                <option value="">Semua</option>
                                <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                                <option value="verified" @selected($filters['status'] === 'verified')>Verified</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="type-desktop" value="Jenis outlet" />
                            <select id="type-desktop" name="type" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                <option value="">Semua</option>
                                <option value="prospek" @selected($filters['type'] === 'prospek')>Prospek</option>
                                <option value="noo" @selected($filters['type'] === 'noo')>NOO</option>
                                <option value="pelanggan_lama" @selected($filters['type'] === 'pelanggan_lama')>Pelanggan Lama</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="outlet_status-desktop" value="Status outlet" />
                            <select id="outlet_status-desktop" name="outlet_status" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                                <option value="">Semua</option>
                                <option value="active" @selected($filters['outlet_status'] === 'active')>Active</option>
                                <option value="inactive" @selected($filters['outlet_status'] === 'inactive')>Inactive</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2 xl:col-span-5 flex gap-3">
                            <x-primary-button class="flex-1 sm:flex-none">Terapkan Filter</x-primary-button>
                            <a href="{{ route('outlets.index') }}" class="inline-flex flex-1 items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100 sm:flex-none sm:px-5">Reset</a>
                        </div>
                    </div>
                </form>
            </section>

            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block shadow-[0_18px_40px_-30px_rgba(15,23,42,0.28)]">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Cabang</th>
                                <th class="px-4 py-3 font-semibold">Jenis</th>
                                <th class="px-4 py-3 font-semibold">Official Kode</th>
                                <th class="px-4 py-3 font-semibold">Status Outlet</th>
                                <th class="px-4 py-3 font-semibold">Verifikasi</th>
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
                                    <td class="px-4 py-4 text-slate-600">{{ str($outlet->outlet_type)->replace('_', ' ')->title() }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->official_kode ?: '-' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->outlet_status === 'inactive' ? 'bg-slate-200 text-slate-700' : 'bg-sky-50 text-sky-700' }}">
                                            {{ $outlet->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->verification_status === 'verified' ? 'bg-emerald-50 text-emerald-700' : ($outlet->verification_status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500') }}">
                                            {{ $outlet->verificationLabel() }}
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
                                    <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada outlet sesuai filter saat ini.</td>
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
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->verification_status === 'verified' ? 'bg-emerald-50 text-emerald-700' : ($outlet->verification_status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-500') }}">{{ $outlet->verificationLabel() }}</span>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-2.5 text-[13px] text-slate-600">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Cabang</p>
                                    <p class="mt-0.5 leading-5 line-clamp-2">{{ $outlet->branch?->name }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Jenis</p>
                                    <p class="mt-0.5 leading-5">{{ str($outlet->outlet_type)->replace('_', ' ')->title() }}</p>
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
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada outlet sesuai filter saat ini.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $outlets->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
