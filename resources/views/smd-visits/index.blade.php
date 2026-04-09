<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="app-overline">Kunjungan SMD</p>
                <h2 class="app-page-title mt-2">Riwayat Aktivitas SMD</h2>
                <p class="app-body-copy mt-2 max-w-3xl">Pantau aktivitas PO, display, tukar faktur, dan tagihan yang sudah dikerjakan tim SMD.</p>
            </div>
            <a href="{{ route('smd-visits.create') }}" class="app-action-primary">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14" /></svg>
                Input Kunjungan SMD
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
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
                            <x-text-input id="search" name="search" class="app-field-with-icon block w-full" :value="$filters['search']" placeholder="Mis. Toko Sinar atau OFF-BDG-001" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="activity" value="Aktivitas" />
                        <select id="activity" name="activity" class="app-select mt-2 block w-full">
                            <option value="">Semua</option>
                            <option value="ambil_po" @selected($filters['activity'] === 'ambil_po')>Ambil PO</option>
                            <option value="merapikan_display" @selected($filters['activity'] === 'merapikan_display')>Merapikan Display</option>
                            <option value="tukar_faktur" @selected($filters['activity'] === 'tukar_faktur')>Tukar Faktur</option>
                            <option value="ambil_tagihan" @selected($filters['activity'] === 'ambil_tagihan')>Ambil Tagihan</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap gap-3">
                        <x-primary-button>Terapkan Filter</x-primary-button>
                        <a href="{{ route('smd-visits.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
                <div class="app-table-shell hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Aktivitas</th>
                                <th class="px-4 py-3 font-semibold">Nominal PO</th>
                                <th class="px-4 py-3 font-semibold">Nominal Pembayaran</th>
                                <th class="px-4 py-3 font-semibold">Waktu</th>
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
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->smdActivities->pluck('activity_type')->map(fn ($item) => str($item)->replace('_', ' ')->title())->implode(', ') }}</td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->smdDetail?->po_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->smdDetail?->payment_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->user?->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada kunjungan SMD yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($visits as $visit)
                        <div class="app-soft-panel p-4">
                            <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</p>
                            <p class="mt-3 text-sm text-slate-600">{{ $visit->smdActivities->pluck('activity_type')->map(fn ($item) => str($item)->replace('_', ' ')->title())->implode(', ') }}</p>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada kunjungan SMD yang tersimpan.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
