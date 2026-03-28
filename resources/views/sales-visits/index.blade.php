<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kunjungan Sales</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Riwayat Kunjungan</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">Pantau kunjungan sales yang sudah masuk, status outlet buka atau tutup, dan nominal transaksi saat outlet buka.</p>
            </div>
            <a href="{{ route('sales-visits.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-ink-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800">
                Input Kunjungan Sales
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('status') }}</div>
            @endif

            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <form method="GET" class="grid gap-3 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Cari outlet / official kode" />
                        <x-text-input id="search" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Mis. Salon Mawar atau OFF-BDG-001" />
                    </div>
                    <div>
                        <x-input-label for="condition" value="Kondisi outlet" />
                        <select id="condition" name="condition" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                            <option value="">Semua</option>
                            <option value="buka" @selected($filters['condition'] === 'buka')>Buka</option>
                            <option value="tutup" @selected($filters['condition'] === 'tutup')>Tutup</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap gap-3">
                        <x-primary-button>Terapkan Filter</x-primary-button>
                        <a href="{{ route('sales-visits.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-200/60 transition hover:border-slate-300 hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </section>

            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Waktu</th>
                                <th class="px-4 py-3 font-semibold">Kondisi</th>
                                <th class="px-4 py-3 font-semibold">Nominal Order</th>
                                <th class="px-4 py-3 font-semibold">Total Tagihan</th>
                                <th class="px-4 py-3 font-semibold">Pelaksana</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($visits as $visit)
                                <tr>
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $visit->outlet?->district }}, {{ $visit->outlet?->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->visited_at?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $visit->outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ ucfirst($visit->outlet_condition) }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->salesDetail?->order_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->salesDetail?->receivable_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->user?->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada kunjungan sales yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($visits as $visit)
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $visit->visited_at?->format('d M Y H:i') }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $visit->outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ ucfirst($visit->outlet_condition) }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Order</p>
                                    <p class="mt-1">Rp {{ number_format((float) $visit->salesDetail?->order_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Tagihan</p>
                                    <p class="mt-1">Rp {{ number_format((float) $visit->salesDetail?->receivable_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada kunjungan sales yang tersimpan.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
