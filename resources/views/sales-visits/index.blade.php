<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="app-badge app-badge-sky">Kunjungan Sales</span>
                <h2 class="app-page-title mt-2">Riwayat Kunjungan</h2>
                <p class="app-body-copy mt-2 max-w-3xl">Pantau kunjungan sales yang sudah masuk, termasuk outlet buka, tutup, atau order by WA.</p>
            </div>
            <a href="{{ route('sales-visits.create') }}" class="app-action-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14" /></svg>
                Input Kunjungan Sales
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Filter Section --}}
            <section x-data="{ filtersOpen: false }" class="app-panel p-4">
                <form method="GET">
                    {{-- Mobile: search + toggle --}}
                    <div class="flex items-end gap-3 lg:hidden">
                        <div class="flex-1">
                            <x-input-label for="search_mobile" value="Cari outlet / official kode" />
                            <div class="app-input-shell mt-2">
                                <span class="app-input-icon">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                                </span>
                                <x-text-input id="search_mobile" name="search" class="app-field-with-icon block w-full" :value="$filters['search']" placeholder="Mis. Salon Mawar atau OFF-BDG-001" />
                            </div>
                        </div>
                        <button type="button" @click="filtersOpen = !filtersOpen" class="app-action-secondary min-h-[3rem] px-4 py-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2.586a1 1 0 0 1-.293.707l-6.414 6.414a1 1 0 0 0-.293.707V17l-4 4v-6.586a1 1 0 0 0-.293-.707L3.293 7.293A1 1 0 0 1 3 6.586V4z" /></svg>
                            <span x-text="filtersOpen ? 'Tutup' : 'Filter'">Filter</span>
                        </button>
                    </div>

                    {{-- Desktop: all filters visible / Mobile: collapsible --}}
                    <div :class="filtersOpen ? '' : 'hidden'" class="mt-4 lg:mt-0 lg:!block">
                        <div class="grid gap-3 md:grid-cols-4">
                            <div class="hidden md:col-span-3 lg:block">
                                <x-input-label for="search_desktop" value="Cari outlet / official kode" />
                                <div class="app-input-shell mt-2">
                                    <span class="app-input-icon">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                                    </span>
                                    <x-text-input id="search_desktop" name="search" class="app-field-with-icon block w-full" :value="$filters['search']" placeholder="Mis. Salon Mawar atau OFF-BDG-001" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="condition" value="Kondisi outlet" />
                                <select id="condition" name="condition" class="app-select mt-2 block w-full">
                                    <option value="">Semua</option>
                                    <option value="buka" @selected($filters['condition'] === 'buka')>Buka</option>
                                    <option value="tutup" @selected($filters['condition'] === 'tutup')>Tutup</option>
                                    <option value="order_by_wa" @selected($filters['condition'] === 'order_by_wa')>Order by WA</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-3">
                            <button type="submit" class="app-action-primary">Terapkan</button>
                            <a href="{{ route('sales-visits.index') }}" class="app-action-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </section>

            {{-- Data Section --}}
            <section class="app-panel p-4 sm:p-6">

                {{-- Desktop Table --}}
                <div class="app-table-shell hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Waktu</th>
                                <th class="px-4 py-3 font-semibold">Kondisi</th>
                                <th class="px-4 py-3 font-semibold">Nominal Order</th>
                                <th class="px-4 py-3 font-semibold">Total Tagihan</th>
                                <th class="px-4 py-3 font-semibold">Pelaksana</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($visits as $visit)
                                <tr class="transition duration-200 hover:bg-sky-50/60">
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $visit->outlet?->district }}, {{ $visit->outlet?->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4">
                                        <span class="app-badge {{ $visit->outlet_condition === 'buka' ? 'app-badge-emerald' : ($visit->outlet_condition === 'order_by_wa' ? 'app-badge-violet' : 'app-badge-rose') }}">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ucfirst($visit->outlet_condition) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->salesDetail?->order_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->salesDetail?->receivable_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->user?->name }}</td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('visit-history.show', $visit) }}" class="app-btn-sm">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7" /></svg>
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">
                                        <div class="app-empty-state mx-4 my-2">
                                            <p>Belum ada kunjungan sales yang tersimpan.</p>
                                            <a href="{{ route('sales-visits.create') }}" class="app-btn-sm-primary mt-4 inline-flex gap-1.5">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14" /></svg>
                                                Input Kunjungan Baru
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="space-y-3 lg:hidden">
                    @forelse ($visits as $visit)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                <p class="shrink-0 text-xs text-slate-400">{{ $visit->visitedAtForBranch()?->format('d M H:i') }}</p>
                            </div>

                            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Order</p>
                                    <p class="mt-1 font-semibold text-slate-700">Rp {{ number_format((float) $visit->salesDetail?->order_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-wider text-slate-400">Tagihan</p>
                                    <p class="mt-1 font-semibold text-slate-700">Rp {{ number_format((float) $visit->salesDetail?->receivable_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="app-badge {{ $visit->outlet_condition === 'buka' ? 'app-badge-emerald' : ($visit->outlet_condition === 'order_by_wa' ? 'app-badge-violet' : 'app-badge-rose') }}">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ucfirst($visit->outlet_condition) }}</span>
                                <a href="{{ route('visit-history.show', $visit) }}" class="app-btn-sm-primary">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="app-empty-state">
                            <p>Belum ada kunjungan sales yang tersimpan.</p>
                            <a href="{{ route('sales-visits.create') }}" class="app-btn-sm-primary mt-4 inline-flex gap-1.5">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14" /></svg>
                                Input Kunjungan Baru
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
