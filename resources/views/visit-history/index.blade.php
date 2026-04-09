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
        <div>
            <div class="flex items-center gap-2">
                <span class="app-chip">Monitoring Operasional</span>
                <span class="app-chip">History Kunjungan</span>
            </div>
            <h2 class="app-page-title mt-4">History Kunjungan</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="app-panel app-animate-enter overflow-hidden p-4 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="app-overline">Ringkasan Scope Aktif</p>
                        <h3 class="app-section-title mt-2">Riwayat kunjungan yang sedang kamu lihat</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="app-kpi min-w-[9rem]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Total Visit</p>
                            <p class="mt-3 text-2xl font-semibold text-ink-950">{{ $totalVisits }}</p>
                        </div>
                        <div class="app-kpi min-w-[9rem]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Sales</p>
                            <p class="mt-3 text-2xl font-semibold text-ink-950">{{ $salesVisits }}</p>
                        </div>
                        <div class="app-kpi min-w-[9rem]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">SMD</p>
                            <p class="mt-3 text-2xl font-semibold text-ink-950">{{ $smdVisits }}</p>
                        </div>
                        <div class="app-kpi min-w-[9rem]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400">Collection</p>
                            <p class="mt-3 text-lg font-semibold text-ink-950">Rp {{ number_format($totalCollectionAmount, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="app-panel app-animate-enter p-4 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="app-overline">Filter</p>
                        <h3 class="app-section-title mt-2">Temukan kunjungan yang kamu butuhkan</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="app-chip">Order Rp {{ number_format($totalSalesAmount, 0, ',', '.') }}</span>
                        <span class="app-chip">Collection Rp {{ number_format($totalCollectionAmount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form method="GET" class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-6">
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
                    <div class="md:col-span-2 xl:col-span-2">
                        <x-input-label for="search" value="Cari user / outlet" />
                        <div class="app-input-shell mt-2">
                            <span class="app-input-icon">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                            </span>
                            <x-text-input id="search" name="search" class="app-field-with-icon block w-full" :value="$filters['search']" placeholder="Nama user atau outlet" />
                        </div>
                    </div>
                    <div class="md:col-span-2 xl:col-span-6 flex flex-col gap-3 sm:flex-row">
                        <x-primary-button class="justify-center sm:min-w-[180px]">Terapkan</x-primary-button>
                        <a href="{{ route('visit-history.index') }}" class="app-glass-button justify-center sm:min-w-[140px]">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel app-animate-enter p-4 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Daftar History</p>
                        <h3 class="app-section-title mt-2">Urutan kunjungan terbaru</h3>
                    </div>
                    <span class="app-chip">{{ $visits->total() }} data</span>
                </div>

                <div class="mt-5 hidden overflow-hidden rounded-[1.65rem] border border-slate-200/90 lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5fe_100%)] text-left text-slate-500">
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
                        <tbody class="divide-y divide-slate-100 bg-white/90">
                            @forelse ($visits as $visit)
                                <tr class="transition duration-200 hover:bg-sky-50/60">
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4 text-slate-900">{{ $visit->user?->name }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->outlet?->name }}</td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $visit->visit_type === 'sales' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                                            {{ $visit->typeLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $visit->outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : ($visit->outlet_condition === 'order_by_wa' ? 'bg-violet-50 text-violet-700' : ($visit->outlet_condition === 'tutup' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600')) }}">
                                            {{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ($visit->outlet_condition ?: '-') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-4"><a href="{{ route('visit-history.show', $visit) }}" class="app-action-secondary min-h-[2.75rem] px-4 py-2 text-[13px]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7" /></svg>Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada history kunjungan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 space-y-3 lg:hidden">
                    @forelse ($visits as $visit)
                        <div class="app-soft-panel app-card-interactive px-4 py-4 text-sm text-slate-600">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $visit->visit_type === 'sales' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $visit->typeLabel() }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">{{ $visit->user?->name }}</p>
                                </div>
                                <p class="text-xs text-slate-400">{{ $visit->visitedAtForBranch()?->format('d M H:i') }}</p>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Sales Amount</p>
                                    <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</p>
                                </div>
                                <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Collection</p>
                                    <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold {{ $visit->outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : ($visit->outlet_condition === 'order_by_wa' ? 'bg-violet-50 text-violet-700' : ($visit->outlet_condition === 'tutup' ? 'bg-amber-50 text-amber-700' : 'bg-white text-slate-600')) }}">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ($visit->outlet_condition ?: '-') }}</span>
                                <a href="{{ route('visit-history.show', $visit) }}" class="app-action-secondary min-h-[2.75rem] px-4 py-2 text-[13px]"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7" /></svg>Detail</a>
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
