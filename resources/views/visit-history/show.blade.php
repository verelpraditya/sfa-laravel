<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">History Kunjungan</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Detail Kunjungan</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $visit->typeLabel() }} · {{ $visit->outlet?->name }} · {{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</p>
            </div>
            <a href="{{ route('visit-history.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Kembali ke History</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="app-panel p-5">
                <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                    <div class="app-soft-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Informasi Kunjungan</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 text-sm text-slate-600">
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">User</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->user?->name }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Cabang</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->branch?->name }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Outlet</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->outlet?->name }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Tipe</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->typeLabel() }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Kondisi</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->outlet_condition ?: '-' }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Waktu</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Latitude</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->latitude }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Longitude</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->longitude }}</p></div>
                        </div>
                    </div>

                    <div class="app-soft-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Nilai Kunjungan</p>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 text-sm text-slate-600">
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Sales Amount</p><p class="mt-1 text-2xl font-semibold text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</p></div>
                            <div><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Collection</p><p class="mt-1 text-2xl font-semibold text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</p></div>
                        </div>

                        @if ($visit->visit_type === 'smd')
                            <div class="mt-5 border-t border-slate-200 pt-4 text-sm text-slate-600">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Aktivitas SMD</p>
                                <p class="mt-2 font-semibold text-slate-900">{{ $visit->smdActivities->pluck('activity_type')->map(fn ($item) => str($item)->replace('_', ' ')->title())->implode(', ') ?: '-' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="app-panel p-5">
                <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                    <div class="app-soft-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Foto Bukti</p>
                        <img src="{{ asset('storage/'.$visit->visit_photo_path) }}" alt="Foto bukti kunjungan" class="mt-4 w-full rounded-[1.25rem] border border-slate-200 object-cover">
                    </div>

                    <div class="app-soft-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Catatan</p>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $visit->notes ?: 'Tidak ada catatan tambahan.' }}</p>

                        @if ($visit->visit_type === 'smd' && $visit->smdDetail?->display_photo_path)
                            <div class="mt-6 border-t border-slate-200 pt-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Foto Display</p>
                                <img src="{{ asset('storage/'.$visit->smdDetail->display_photo_path) }}" alt="Foto display" class="mt-4 w-full rounded-[1.25rem] border border-slate-200 object-cover">
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
