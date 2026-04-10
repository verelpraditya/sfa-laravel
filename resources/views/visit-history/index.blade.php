<x-app-layout>
    @php
        $summaryVisits = $visits->getCollection();
        $totalVisits = $summaryVisits->count();
        $salesVisits = $summaryVisits->where('visit_type', 'sales')->count();
        $smdVisits = $summaryVisits->where('visit_type', 'smd')->count();
        $totalSalesAmount = $summaryVisits->sum(fn ($visit) => $visit->salesAmount());
        $totalCollectionAmount = $summaryVisits->sum(fn ($visit) => $visit->collectionAmount());
    @endphp

    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <p class="text-sm font-medium text-white/70">Monitoring Operasional</p>
            <h2 class="mt-1 text-2xl font-bold text-white sm:text-3xl">History Kunjungan</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- KPI Section --}}
            <section class="app-animate-enter">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="app-kpi-blue">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-blue-600">Total Visit</p>
                        <p class="mt-2 pl-2 text-xl font-bold text-slate-900 sm:mt-3 sm:text-2xl">{{ $totalVisits }}</p>
                    </div>
                    <div class="app-kpi-sky">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-sky-600">Sales</p>
                        <p class="mt-2 pl-2 text-xl font-bold text-slate-900 sm:mt-3 sm:text-2xl">{{ $salesVisits }}</p>
                    </div>
                    <div class="app-kpi-violet">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-violet-600">SMD</p>
                        <p class="mt-2 pl-2 text-xl font-bold text-slate-900 sm:mt-3 sm:text-2xl">{{ $smdVisits }}</p>
                    </div>
                    <div class="app-kpi-emerald">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-emerald-600">Collection</p>
                        <p class="mt-2 pl-2 text-base font-bold text-slate-900 sm:mt-3 sm:text-lg">Rp {{ number_format($totalCollectionAmount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </section>

            {{-- Filter Section --}}
            <section class="app-panel app-animate-enter rounded-xl p-4"
                     x-data="{ filtersOpen: false, search: '{{ str_replace("'", "\\'", $filters['search'] ?? '') }}' }">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="app-overline">Filter</p>
                        <h3 class="app-section-title mt-2">Temukan kunjungan yang kamu butuhkan</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-100 px-3 py-1.5 text-xs font-bold text-sky-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Order Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Collection Rp {{ number_format($totalCollectionAmount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <form method="GET" action="{{ route('visit-history.index') }}" class="mt-5">
                    {{-- Hidden search field synced via Alpine --}}
                    <input type="hidden" name="search" :value="search">

                    {{-- Mobile: always-visible search + toggle --}}
                    <div class="flex items-end gap-2 lg:hidden">
                        <div class="flex-1">
                            <x-input-label for="search_mobile" value="Cari user / outlet" />
                            <div class="app-input-shell mt-2">
                                <span class="app-input-icon">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                                </span>
                                <input id="search_mobile" type="text" x-model="search" class="app-field-with-icon block w-full" placeholder="Nama user atau outlet" />
                            </div>
                        </div>
                        <button type="button" class="app-action-secondary min-h-[3rem] px-4" @click="filtersOpen = !filtersOpen">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .8 1.6L14 13.28V19a1 1 0 0 1-.55.9l-4 2A1 1 0 0 1 8 21v-7.72L1.2 4.6A1 1 0 0 1 2 3h1" /></svg>
                            <span x-text="filtersOpen ? 'Tutup' : 'Filter'">Filter</span>
                        </button>
                    </div>

                    {{-- Collapsible on mobile, always visible on desktop --}}
                    <div class="mt-3 hidden lg:mt-0 lg:!block"
                         :class="{ '!block': filtersOpen }">
                        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                            <div>
                                <x-input-label for="from" value="Dari tanggal" />
                                <x-text-input id="from" name="from" type="date" class="mt-2 block w-full" :value="$filters['from']" />
                            </div>
                            <div>
                                <x-input-label for="to" value="Sampai tanggal" />
                                <x-text-input id="to" name="to" type="date" class="mt-2 block w-full" :value="$filters['to']" />
                            </div>
                            <div>
                                <x-input-label for="type" value="Tipe" />
                                <select id="type" name="type" class="app-select mt-2 block w-full">
                                    <option value="">Semua</option>
                                    <option value="sales" @selected($filters['type'] === 'sales')>Sales</option>
                                    <option value="smd" @selected($filters['type'] === 'smd')>SMD</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="condition" value="Kondisi" />
                                <select id="condition" name="condition" class="app-select mt-2 block w-full">
                                    <option value="">Semua</option>
                                    <option value="buka" @selected($filters['condition'] === 'buka')>Buka</option>
                                    <option value="tutup" @selected($filters['condition'] === 'tutup')>Tutup</option>
                                    <option value="order_by_wa" @selected($filters['condition'] === 'order_by_wa')>Order by WA</option>
                                </select>
                            </div>
                            {{-- Desktop search (hidden on mobile since mobile search is above) --}}
                            <div class="hidden lg:block xl:col-span-2">
                                <x-input-label for="search_desktop" value="Cari user / outlet" />
                                <div class="app-input-shell mt-2">
                                    <span class="app-input-icon">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                                    </span>
                                    <input id="search_desktop" type="text" x-model="search" class="app-field-with-icon block w-full" placeholder="Nama user atau outlet" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="app-action-primary justify-center sm:min-w-[180px]">Terapkan</button>
                        <a href="{{ route('visit-history.index') }}" class="app-action-secondary justify-center sm:min-w-[140px]">Reset</a>
                    </div>
                </form>
            </section>

            {{-- Data Section --}}
            <section class="app-panel app-animate-enter rounded-xl p-4 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Daftar History</p>
                        <h3 class="app-section-title mt-2">Urutan kunjungan terbaru</h3>
                    </div>
                    <span class="app-chip">{{ $visits->total() }} data</span>
                </div>

                {{-- Desktop Table --}}
                <div class="app-table-shell mt-5 hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3.5 font-semibold">Waktu</th>
                                <th class="px-4 py-3.5 font-semibold">User</th>
                                <th class="px-4 py-3.5 font-semibold">Outlet</th>
                                <th class="px-4 py-3.5 font-semibold">Tipe</th>
                                <th class="px-4 py-3.5 font-semibold">Kondisi</th>
                                <th class="px-4 py-3.5 font-semibold">Sales Amount</th>
                                <th class="px-4 py-3.5 font-semibold">Collection</th>
                                <th class="px-4 py-3.5 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($visits as $visit)
                                <tr class="transition duration-150 hover:bg-slate-50">
                                    <td class="px-4 py-3.5 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3.5 text-slate-900">{{ $visit->user?->name }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $visit->outlet?->name }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="app-badge {{ $visit->visit_type === 'sales' ? 'app-badge-sky' : 'app-badge-violet' }}">
                                            {{ $visit->typeLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="app-badge {{ $visit->outlet_condition === 'buka' ? 'app-badge-emerald' : ($visit->outlet_condition === 'tutup' ? 'app-badge-rose' : ($visit->outlet_condition === 'order_by_wa' ? 'app-badge-violet' : 'app-badge-slate')) }}">
                                            {{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ($visit->outlet_condition ?: '-') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5 text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('visit-history.show', $visit) }}" class="app-btn-sm">Detail</a>
                                            @if (auth()->user()->isAdminPusat() || auth()->user()->isSupervisor())
                                                <a href="{{ route('visit-history.edit', $visit) }}" class="app-btn-sm">Edit</a>
                                                <form method="POST" action="{{ route('visit-history.destroy', $visit) }}" onsubmit="return confirm('Yakin hapus kunjungan ini? Data dan foto akan dihapus permanen.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="app-btn-sm text-rose-600 hover:bg-rose-50">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-0">
                                        <div class="app-empty-state m-4">Belum ada history kunjungan.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards --}}
                <div class="mt-5 space-y-3 lg:hidden">
                    @forelse ($visits as $visit)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                            {{-- Header: user + outlet + time --}}
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-slate-900">{{ $visit->user?->name }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $visit->outlet?->name }}</p>
                                </div>
                                <p class="shrink-0 text-xs text-slate-400">{{ $visit->visitedAtForBranch()?->format('d M H:i') }}</p>
                            </div>

                            {{-- Badges inline --}}
                            <div class="mt-2.5 flex flex-wrap gap-1.5">
                                <span class="app-badge {{ $visit->visit_type === 'sales' ? 'app-badge-sky' : 'app-badge-violet' }}">{{ $visit->typeLabel() }}</span>
                                <span class="app-badge {{ $visit->outlet_condition === 'buka' ? 'app-badge-emerald' : ($visit->outlet_condition === 'tutup' ? 'app-badge-rose' : ($visit->outlet_condition === 'order_by_wa' ? 'app-badge-violet' : 'app-badge-slate')) }}">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ($visit->outlet_condition ?: '-') }}</span>
                            </div>

                            {{-- Amounts --}}
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <div class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-2.5">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-600">Sales Amount</p>
                                    <p class="mt-0.5 text-sm font-bold text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</p>
                                </div>
                                <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2.5">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-600">Collection</p>
                                    <p class="mt-0.5 text-sm font-bold text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</p>
                                </div>
                            </div>

                            {{-- Action --}}
                            <div class="mt-3 flex items-center justify-end gap-2">
                                @if (auth()->user()->isAdminPusat() || auth()->user()->isSupervisor())
                                    <a href="{{ route('visit-history.edit', $visit) }}" class="app-btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('visit-history.destroy', $visit) }}" onsubmit="return confirm('Yakin hapus kunjungan ini? Data dan foto akan dihapus permanen.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="app-btn-sm text-rose-600 hover:bg-rose-50">Hapus</button>
                                    </form>
                                @endif
                                <a href="{{ route('visit-history.show', $visit) }}" class="app-btn-sm-primary">Lihat Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada history kunjungan.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
