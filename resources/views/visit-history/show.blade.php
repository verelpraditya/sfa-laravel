@php
    $latitude = is_numeric($visit->latitude) ? (float) $visit->latitude : null;
    $longitude = is_numeric($visit->longitude) ? (float) $visit->longitude : null;
    $hasCoordinates = $latitude !== null && $longitude !== null;
    $mapDelta = 0.005;
    $bbox = $hasCoordinates ? implode(',', [$longitude - $mapDelta, $latitude - $mapDelta, $longitude + $mapDelta, $latitude + $mapDelta]) : null;

    $conditionColor = match($visit->outlet_condition) {
        'buka'        => 'bg-emerald-100 text-emerald-700',
        'order_by_wa' => 'bg-violet-100 text-violet-700',
        'tutup'       => 'bg-rose-100 text-rose-700',
        default       => 'bg-slate-100 text-slate-600',
    };
    $conditionLabel = match($visit->outlet_condition) {
        'buka'        => 'Buka',
        'order_by_wa' => 'Order by WA',
        'tutup'       => 'Tutup',
        default       => '-',
    };
    $typeColor = $visit->visit_type === 'sales' ? 'bg-sky-100 text-sky-700' : 'bg-violet-100 text-violet-700';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">History Kunjungan</span>
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">{{ $visit->typeLabel() }}</span>
                    </div>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">Detail Kunjungan</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('visit-history.index') }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                        Kembali
                    </a>
                    @if (auth()->user()->isAdminPusat() || auth()->user()->isSupervisor())
                        <a href="{{ route('visit-history.edit', $visit) }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <button type="button" onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-rose-500/80 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            {{-- ═══════════════════════════════════════════════
                 BARIS 1: Snapshot + KPI Amount
            ══════════════════════════════════════════════════ --}}
            <section class="app-animate-enter">
                <div class="grid gap-4 xl:grid-cols-[1.2fr_0.8fr]">

                    {{-- Snapshot --}}
                    <div class="app-panel relative overflow-hidden p-5 sm:p-6">
                        <div class="absolute inset-x-0 top-0 h-1 app-hero-gradient"></div>
                        <div class="flex items-start gap-4">
                            {{-- Ikon outlet --}}
                            <div class="mt-0.5 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-sky-600">Outlet</p>
                                <div class="mt-1 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                    <h3 class="text-xl font-bold leading-tight text-slate-900 sm:text-2xl">{{ $visit->outlet?->name }}</h3>
                                    @if ($visit->outlet?->official_kode)
                                        <a href="{{ route('outlets.show', $visit->outlet) }}" class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2.5 py-1 font-mono text-xs font-bold text-slate-600 transition hover:bg-sky-100 hover:text-sky-700" title="Lihat detail outlet">
                                            <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            {{ $visit->outlet->official_kode }}
                                            <svg class="h-2.5 w-2.5 shrink-0 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $visit->user?->name }}
                                    </span>
                                    <span class="text-slate-300">·</span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $visit->branch?->name }}
                                    </span>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center rounded-full {{ $typeColor }} px-3 py-1 text-xs font-bold">{{ $visit->typeLabel() }}</span>
                                    <span class="inline-flex items-center rounded-full {{ $conditionColor }} px-3 py-1 text-xs font-bold">{{ $conditionLabel }}</span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $visit->visitedAtForBranch()?->format('d M Y, H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KPI Amount — hanya Sales Amount & Collection --}}
                    <div class="grid grid-cols-2 gap-3 xl:grid-cols-1">
                        <div class="app-kpi-sky">
                            <div class="flex items-center gap-2 pl-2">
                                <svg class="h-4 w-4 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-sky-600">{{ $visit->visit_type === 'smd' ? 'PO Amount' : 'Sales Amount' }}</p>
                            </div>
                            <p class="mt-2 pl-2 text-xl font-bold text-slate-900 sm:text-2xl">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</p>
                        </div>
                        <div class="app-kpi-emerald">
                            <div class="flex items-center gap-2 pl-2">
                                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-600">Collection</p>
                            </div>
                            <p class="mt-2 pl-2 text-xl font-bold text-slate-900 sm:text-2xl">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</p>
                        </div>
                    </div>

                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 BARIS 2: Info + Map/Foto
            ══════════════════════════════════════════════════ --}}
            <section class="grid gap-5 xl:grid-cols-[0.95fr_1.05fr]">

                {{-- Kolom Kiri --}}
                <div class="space-y-5">

                    {{-- Informasi Kunjungan — list rows dengan ikon --}}
                    <div class="app-panel app-animate-enter overflow-hidden">
                        {{-- Header panel --}}
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <h3 class="text-sm font-bold text-slate-800">Informasi Kunjungan</h3>
                            </div>
                        </div>

                        {{-- Rows --}}
                        <div class="divide-y divide-slate-50">
                            {{-- User --}}
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">User</p>
                                    <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $visit->user?->name ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Cabang --}}
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Cabang</p>
                                    <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $visit->branch?->name ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Outlet --}}
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Outlet</p>
                                    <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $visit->outlet?->name ?? '-' }}</p>
                                </div>
                            </div>

                            {{-- Tipe --}}
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Tipe Kunjungan</p>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center rounded-full {{ $typeColor }} px-2.5 py-0.5 text-xs font-bold">{{ $visit->typeLabel() }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kondisi --}}
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Kondisi Outlet</p>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center rounded-full {{ $conditionColor }} px-2.5 py-0.5 text-xs font-bold">{{ $conditionLabel }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Waktu --}}
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Waktu Kunjungan</p>
                                    <p class="mt-0.5 text-sm font-semibold text-slate-900">{{ $visit->visitedAtForBranch()?->format('d M Y, H:i') ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Aktivitas SMD (jika ada) --}}
                    @if ($visit->visit_type === 'smd')
                        <div class="app-panel app-animate-enter overflow-hidden">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    <h3 class="text-sm font-bold text-slate-800">Aktivitas SMD</h3>
                                </div>
                            </div>
                            <div class="px-5 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @forelse ($visit->smdActivities as $activity)
                                        <span class="app-badge app-badge-violet">{{ str($activity->activity_type)->replace('_', ' ')->title() }}</span>
                                    @empty
                                        <p class="text-sm text-slate-400">Tidak ada aktivitas tercatat.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Catatan --}}
                    <div class="app-panel app-animate-enter overflow-hidden">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <h3 class="text-sm font-bold text-slate-800">Catatan</h3>
                            </div>
                        </div>
                        <div class="px-5 py-4">
                            <p class="text-sm leading-relaxed text-slate-{{ $visit->notes ? '700' : '400' }}">{{ $visit->notes ?: 'Tidak ada catatan tambahan.' }}</p>
                        </div>
                    </div>

                </div>

                {{-- Kolom Kanan --}}
                <div class="space-y-5">

                    {{-- Lokasi (peta + koordinat) --}}
                    @if ($hasCoordinates)
                        <div class="app-panel app-animate-enter overflow-hidden">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <h3 class="text-sm font-bold text-slate-800">Lokasi Kunjungan</h3>
                                </div>
                                <a href="{{ 'https://www.google.com/maps?q='.$latitude.','.$longitude }}" target="_blank" rel="noopener noreferrer" class="app-btn-sm-primary gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                    Google Maps
                                </a>
                            </div>

                            {{-- Peta --}}
                            <div class="overflow-hidden">
                                <iframe
                                    title="Peta lokasi kunjungan"
                                    src="{{ 'https://www.openstreetmap.org/export/embed.html?bbox='.$bbox.'&layer=mapnik&marker='.$latitude.','.$longitude }}"
                                    class="h-[17rem] w-full border-0 sm:h-[20rem]"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                ></iframe>
                            </div>

                            {{-- Koordinat + OSM link --}}
                            <div class="border-t border-slate-100 px-5 py-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    {{-- Koordinat sebagai rows kecil --}}
                                    <div class="flex items-center gap-4 text-sm">
                                        <div class="flex items-center gap-2 text-slate-500">
                                            <svg class="h-3.5 w-3.5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span class="font-mono text-xs font-semibold text-slate-700">{{ $latitude }}, {{ $longitude }}</span>
                                        </div>
                                    </div>
                                    <a href="{{ 'https://www.openstreetmap.org/?mlat='.$latitude.'&mlon='.$longitude.'#map=18/'.$latitude.'/'.$longitude }}" target="_blank" rel="noopener noreferrer" class="app-btn-sm gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                        OpenStreetMap
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Foto Bukti --}}
                    <div class="app-panel app-animate-enter overflow-hidden">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <h3 class="text-sm font-bold text-slate-800">Foto Bukti Kunjungan</h3>
                            </div>
                        </div>
                        <div class="overflow-hidden rounded-b-xl">
                            <img src="{{ asset('storage/'.$visit->visit_photo_path) }}" alt="Foto bukti kunjungan" class="w-full object-cover">
                        </div>
                    </div>

                    {{-- Foto Display (SMD) --}}
                    @if ($visit->visit_type === 'smd' && ($visit->displayPhotos->isNotEmpty() || $visit->smdDetail?->display_photo_path))
                        <div class="app-panel app-animate-enter overflow-hidden">
                            <div class="border-b border-slate-100 px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <h3 class="text-sm font-bold text-slate-800">Foto Display</h3>
                                </div>
                            </div>
                            @if ($visit->displayPhotos->isNotEmpty())
                                <div class="grid gap-px sm:grid-cols-2">
                                    @foreach ($visit->displayPhotos as $photo)
                                        <div class="{{ $loop->last && $loop->odd ? 'sm:col-span-2' : '' }} overflow-hidden">
                                            <img src="{{ asset('storage/'.$photo->photo_path) }}" alt="Foto display {{ $loop->iteration }}" class="h-56 w-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="overflow-hidden rounded-b-xl">
                                    <img src="{{ asset('storage/'.$visit->smdDetail->display_photo_path) }}" alt="Foto display" class="w-full object-cover">
                                </div>
                            @endif
                        </div>
                    @endif

                </div>
            </section>

        </div>
    </div>

    {{-- Toast notification --}}
    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
             class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-lg">
            {{ session('status') }}
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if (auth()->user()->isAdminPusat() || auth()->user()->isSupervisor())
        <div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Hapus Kunjungan?</h3>
                        <p class="mt-1 text-sm text-slate-500">Data kunjungan, detail, dan semua foto terkait akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')" class="app-action-secondary">Batal</button>
                    <form method="POST" action="{{ route('visit-history.destroy', $visit) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
