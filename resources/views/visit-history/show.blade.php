<x-app-layout>
    @php($latitude = is_numeric($visit->latitude) ? (float) $visit->latitude : null)
    @php($longitude = is_numeric($visit->longitude) ? (float) $visit->longitude : null)
    @php($hasCoordinates = $latitude !== null && $longitude !== null)
    @php($mapDelta = 0.005)
    @php($bbox = $hasCoordinates ? implode(',', [$longitude - $mapDelta, $latitude - $mapDelta, $longitude + $mapDelta, $latitude + $mapDelta]) : null)
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="app-chip">History Kunjungan</span>
                    <span class="app-chip">{{ $visit->typeLabel() }}</span>
                </div>
                <h2 class="app-page-title mt-4">Detail Kunjungan</h2>
            </div>
            <a href="{{ route('visit-history.index') }}" class="app-glass-button justify-center">Kembali ke History</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="app-panel app-animate-enter overflow-hidden p-5 sm:p-6">
                <div class="grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
                    <div class="relative overflow-hidden rounded-[1.9rem] bg-[linear-gradient(135deg,#0f172a_0%,#1d4ed8_100%)] p-5 text-white shadow-[0_24px_60px_-28px_rgba(29,78,216,0.7)]">
                        <div class="absolute -right-8 top-0 h-28 w-28 rounded-full bg-cyan-300/20 blur-3xl"></div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-100/80">Visit Snapshot</p>
                        <h3 class="mt-3 text-2xl font-semibold">{{ $visit->outlet?->name }}</h3>
                        <p class="mt-2 text-sm text-sky-100/85">{{ $visit->user?->name }} · {{ $visit->branch?->name }}</p>
                        <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="rounded-full bg-white/12 px-3 py-1.5 text-white">{{ $visit->typeLabel() }}</span>
                            <span class="rounded-full bg-white/12 px-3 py-1.5 text-white">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</span>
                            <span class="rounded-full bg-white/12 px-3 py-1.5 text-white">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ($visit->outlet_condition ?: 'Tanpa kondisi') }}</span>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="app-kpi">
                            <p class="app-overline !tracking-[0.16em]">Sales Amount</p>
                            <p class="mt-3 text-2xl font-semibold text-ink-950">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</p>
                        </div>
                        <div class="app-kpi">
                            <p class="app-overline !tracking-[0.16em]">Collection</p>
                            <p class="mt-3 text-2xl font-semibold text-ink-950">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</p>
                        </div>
                        <div class="app-kpi">
                            <p class="app-overline !tracking-[0.16em]">Latitude</p>
                            <p class="mt-3 text-sm font-semibold text-ink-950">{{ $visit->latitude }}</p>
                        </div>
                        <div class="app-kpi">
                            <p class="app-overline !tracking-[0.16em]">Longitude</p>
                            <p class="mt-3 text-sm font-semibold text-ink-950">{{ $visit->longitude }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <div class="space-y-6">
                    <section class="app-panel app-animate-enter p-5 sm:p-6">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="app-overline">Informasi Kunjungan</p>
                                <h3 class="app-section-title mt-2">Data utama</h3>
                            </div>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2 text-sm text-slate-600">
                            <div class="app-soft-panel px-4 py-4"><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">User</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->user?->name }}</p></div>
                            <div class="app-soft-panel px-4 py-4"><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Cabang</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->branch?->name }}</p></div>
                            <div class="app-soft-panel px-4 py-4"><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Outlet</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->outlet?->name }}</p></div>
                            <div class="app-soft-panel px-4 py-4"><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Tipe</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->typeLabel() }}</p></div>
                            <div class="app-soft-panel px-4 py-4"><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Kondisi</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ($visit->outlet_condition ?: '-') }}</p></div>
                            <div class="app-soft-panel px-4 py-4"><p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Waktu</p><p class="mt-1 font-semibold text-slate-900">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</p></div>
                        </div>

                        @if ($visit->visit_type === 'smd')
                            <div class="mt-5 app-soft-panel px-4 py-4 text-sm text-slate-600">
                                <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Aktivitas SMD</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @forelse ($visit->smdActivities as $activity)
                                        <span class="inline-flex rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm">{{ str($activity->activity_type)->replace('_', ' ')->title() }}</span>
                                    @empty
                                        <span class="text-sm text-slate-500">-</span>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </section>

                    <section class="app-panel app-animate-enter p-5 sm:p-6">
                        <p class="app-overline">Catatan</p>
                        <div class="mt-4 app-soft-panel px-4 py-4">
                            <p class="text-sm leading-7 text-slate-600">{{ $visit->notes ?: 'Tidak ada catatan tambahan.' }}</p>
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    @if ($hasCoordinates)
                        <section class="app-panel app-animate-enter p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="app-overline">Lokasi Kunjungan</p>
                                    <h3 class="app-section-title mt-2">Titik kunjungan di peta</h3>
                                </div>
                                <a href="{{ 'https://www.google.com/maps?q='.$latitude.','.$longitude }}" target="_blank" rel="noopener noreferrer" class="app-glass-button px-4 py-2.5">Buka Google Maps</a>
                            </div>

                            <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-slate-50">
                                <iframe
                                    title="Peta lokasi kunjungan"
                                    src="{{ 'https://www.openstreetmap.org/export/embed.html?bbox='.$bbox.'&layer=mapnik&marker='.$latitude.','.$longitude }}"
                                    class="h-[18rem] w-full border-0 sm:h-[21rem]"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                ></iframe>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-3 text-sm text-slate-600">
                                <a href="{{ 'https://www.openstreetmap.org/?mlat='.$latitude.'&mlon='.$longitude.'#map=18/'.$latitude.'/'.$longitude }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:text-slate-900">Buka OpenStreetMap</a>
                                <span class="inline-flex items-center rounded-2xl bg-slate-50 px-4 py-2.5">{{ $latitude }}, {{ $longitude }}</span>
                            </div>
                        </section>
                    @endif

                    <section class="app-panel app-animate-enter p-5 sm:p-6">
                        <p class="app-overline">Foto Bukti</p>
                        <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-slate-50">
                            <img src="{{ asset('storage/'.$visit->visit_photo_path) }}" alt="Foto bukti kunjungan" class="w-full object-cover">
                        </div>
                    </section>

                    @if ($visit->visit_type === 'smd' && ($visit->displayPhotos->isNotEmpty() || $visit->smdDetail?->display_photo_path))
                        <section class="app-panel app-animate-enter p-5 sm:p-6">
                            <p class="app-overline">Foto Display</p>
                            @if ($visit->displayPhotos->isNotEmpty())
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    @foreach ($visit->displayPhotos as $photo)
                                        <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-slate-50">
                                            <img src="{{ asset('storage/'.$photo->photo_path) }}" alt="Foto display {{ $loop->iteration }}" class="h-64 w-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-200 bg-slate-50">
                                    <img src="{{ asset('storage/'.$visit->smdDetail->display_photo_path) }}" alt="Foto display" class="w-full object-cover">
                                </div>
                            @endif
                        </section>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
