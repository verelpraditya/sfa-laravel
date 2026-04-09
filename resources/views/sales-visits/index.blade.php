<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="app-overline">Kunjungan Sales</p>
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

            <section class="app-panel p-5">
                <form method="GET" class="grid gap-3 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Cari outlet / official kode" />
                        <div class="app-input-shell mt-2">
                            <span class="app-input-icon">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                            </span>
                            <x-text-input id="search" name="search" class="app-field-with-icon block w-full" :value="$filters['search']" placeholder="Mis. Salon Mawar atau OFF-BDG-001" />
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
                    <div class="md:col-span-4 flex flex-wrap gap-3">
                        <x-primary-button>Terapkan Filter</x-primary-button>
                        <a href="{{ route('sales-visits.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($visits as $visit)
                                <tr>
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $visit->outlet?->district }}, {{ $visit->outlet?->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $visit->outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : ($visit->outlet_condition === 'order_by_wa' ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-700') }}">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ucfirst($visit->outlet_condition) }}</span>
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
                                    <p class="mt-1 text-sm text-slate-500">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $visit->outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : ($visit->outlet_condition === 'order_by_wa' ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-700') }}">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ucfirst($visit->outlet_condition) }}</span>
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
                        <div class="app-empty-state">Belum ada kunjungan sales yang tersimpan.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
