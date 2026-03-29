<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Monitoring Operasional</p>
            <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">History Kunjungan</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">Riwayat lengkap kunjungan dengan filter tanggal, tipe, user, outlet, dan kondisi.</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="app-panel p-4 sm:p-5">
                <form method="GET" class="grid gap-3 md:grid-cols-5 xl:grid-cols-6">
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
                        <select id="type" name="type" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">Semua</option>
                            <option value="sales" @selected($filters['type'] === 'sales')>Sales</option>
                            <option value="smd" @selected($filters['type'] === 'smd')>SMD</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="condition" value="Kondisi" />
                        <select id="condition" name="condition" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">Semua</option>
                            <option value="buka" @selected($filters['condition'] === 'buka')>Buka</option>
                            <option value="tutup" @selected($filters['condition'] === 'tutup')>Tutup</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Cari user / outlet" />
                        <x-text-input id="search" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Nama user atau outlet" />
                    </div>
                    <div class="md:col-span-5 xl:col-span-6 flex gap-3">
                        <x-primary-button>Terapkan</x-primary-button>
                        <a href="{{ route('visit-history.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Waktu</th>
                                <th class="px-4 py-3 font-semibold">User</th>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Tipe</th>
                                <th class="px-4 py-3 font-semibold">Kondisi</th>
                                <th class="px-4 py-3 font-semibold">Sales Amount</th>
                                <th class="px-4 py-3 font-semibold">Collection</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($visits as $visit)
                                <tr>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4 text-slate-900">{{ $visit->user?->name }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->outlet?->name }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->typeLabel() }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->outlet_condition ?: '-' }}</td>
                                    <td class="px-4 py-4 text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-4"><a href="{{ route('visit-history.show', $visit) }}" class="inline-flex items-center rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-900 shadow-sm shadow-sky-100/50">Detail</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada history kunjungan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($visits as $visit)
                        <div class="app-soft-panel px-4 py-4 text-sm text-slate-600">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $visit->typeLabel() }} · {{ $visit->user?->name }}</p>
                                </div>
                                <p class="text-xs text-slate-400">{{ $visit->visitedAtForBranch()?->format('d M H:i') }}</p>
                            </div>
                            <div class="mt-3 grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Sales Amount</p>
                                    <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Collection</p>
                                    <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between border-t border-slate-200 pt-3">
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600">{{ $visit->outlet_condition ?: '-' }}</span>
                                <a href="{{ route('visit-history.show', $visit) }}" class="inline-flex items-center rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-900 shadow-sm shadow-sky-100/50">Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Belum ada history kunjungan.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
