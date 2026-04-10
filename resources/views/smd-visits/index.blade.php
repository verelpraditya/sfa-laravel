<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="app-badge app-badge-sky">Kunjungan SMD</span>
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

            {{-- Filter Section --}}
            <section x-data="{ filtersOpen: false }" class="app-panel p-4">
                <form method="GET">
                    {{-- Always-visible: search bar + mobile filter toggle --}}
                    <div class="flex items-end gap-3">
                        <div class="min-w-0 flex-1">
                            <x-input-label for="search" value="Cari outlet / official kode" class="sr-only lg:not-sr-only" />
                            <div class="app-input-shell mt-0 lg:mt-2">
                                <span class="app-input-icon">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                                </span>
                                <x-text-input id="search" name="search" class="app-field-with-icon block w-full" :value="$filters['search']" placeholder="Mis. Toko Sinar atau OFF-BDG-001" />
                            </div>
                        </div>
                        <button type="button" x-on:click="filtersOpen = !filtersOpen" class="app-action-secondary min-h-[2.75rem] shrink-0 px-4 py-2 lg:hidden">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 4h18M6 8h12M9 12h6" /></svg>
                            Filter
                        </button>
                    </div>

                    {{-- Extra filters: collapsed on mobile, always visible on lg+ --}}
                    <div x-show="filtersOpen" x-collapse x-cloak class="mt-4 lg:!block">
                        <div class="grid gap-3 md:grid-cols-3">
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
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3">
                            <button type="submit" class="app-action-primary min-h-[2.75rem] px-5 py-2 text-sm">Terapkan</button>
                            <a href="{{ route('smd-visits.index') }}" class="app-action-secondary min-h-[2.75rem] px-5 py-2 text-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </section>

            {{-- Data Section --}}
            <section>
                @if ($visits->count())
                    {{-- Desktop Table --}}
                    <div class="app-panel hidden lg:block">
                        <div class="app-table-shell">
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
                                    @foreach ($visits as $visit)
                                        <tr class="transition hover:bg-slate-50/60">
                                            <td class="px-4 py-4 align-top">
                                                <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ $visit->outlet?->district }}, {{ $visit->outlet?->city }}</p>
                                            </td>
                                            <td class="px-4 py-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($visit->smdActivities->pluck('activity_type') as $activity)
                                                        <span class="app-badge app-badge-sky">{{ str($activity)->replace('_', ' ')->title() }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->smdDetail?->po_amount, 0, ',', '.') }}</td>
                                            <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->smdDetail?->payment_amount, 0, ',', '.') }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                            <td class="px-4 py-4 text-slate-600">{{ $visit->user?->name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="space-y-3 lg:hidden">
                        @foreach ($visits as $visit)
                            <div class="app-panel rounded-xl p-4">
                                {{-- Top row: outlet name + waktu --}}
                                <div class="flex items-start justify-between gap-3">
                                    <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                    <span class="shrink-0 text-xs text-slate-500">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</span>
                                </div>

                                {{-- Activity badges --}}
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach ($visit->smdActivities->pluck('activity_type') as $activity)
                                        <span class="app-badge app-badge-sky">{{ str($activity)->replace('_', ' ')->title() }}</span>
                                    @endforeach
                                </div>

                                {{-- Nominals 2-col grid --}}
                                <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.14em] text-slate-400">PO</p>
                                        <p class="mt-0.5 font-medium text-slate-700">Rp {{ number_format((float) $visit->smdDetail?->po_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Pembayaran</p>
                                        <p class="mt-0.5 font-medium text-slate-700">Rp {{ number_format((float) $visit->smdDetail?->payment_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                {{-- Pelaksana --}}
                                <p class="mt-2 text-xs text-slate-500">{{ $visit->user?->name }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-5">{{ $visits->links() }}</div>

                @else
                    {{-- Empty State --}}
                    <div class="app-empty-state">
                        <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5h6" /></svg>
                        <p class="mt-3">Belum ada kunjungan SMD yang tersimpan.</p>
                        <a href="{{ route('smd-visits.create') }}" class="app-action-primary mt-4 inline-flex min-h-[2.75rem] px-5 py-2 text-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M12 5v14" /></svg>
                            Input Kunjungan SMD
                        </a>
                    </div>
                @endif
            </section>

        </div>
    </div>
</x-app-layout>
