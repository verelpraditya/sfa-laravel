@php
    $statusColor = match($outlet->outlet_status) {
        'active'   => 'app-badge-emerald',
        'pending'  => 'app-badge-amber',
        'prospek'  => 'app-badge-violet',
        'inactive' => 'app-badge-rose',
        default    => 'app-badge-slate',
    };
    $categoryLabel = match($outlet->category) {
        'salon'      => 'Salon',
        'toko'       => 'Toko',
        'barbershop' => 'Barbershop',
        'lainnya'    => 'Lainnya',
        default      => '-',
    };

    $lastVisit = $stats['last_visit'];
    $hasCoordinates = $lastVisit && is_numeric($lastVisit->latitude) && is_numeric($lastVisit->longitude);
    $latitude = $hasCoordinates ? (float) $lastVisit->latitude : null;
    $longitude = $hasCoordinates ? (float) $lastVisit->longitude : null;
    $mapDelta = 0.005;
    $bbox = $hasCoordinates ? implode(',', [$longitude - $mapDelta, $latitude - $mapDelta, $longitude + $mapDelta, $latitude + $mapDelta]) : null;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">Detail Outlet</span>
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">{{ $outlet->statusLabel() }}</span>
                    </div>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">{{ $outlet->name }}</h2>
                    <p class="mt-1 text-sm text-white/70">{{ $outlet->district }}, {{ $outlet->city }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('outlets.index') }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                        Kembali
                    </a>
                    @if (auth()->user()->canManageOutletMaster())
                        <a href="{{ route('outlets.edit', $outlet) }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                    @endif
                    @if (auth()->user()->canDeleteOutlets())
                        <button type="button" onclick="document.getElementById('deleteOutletModal').classList.remove('hidden')" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-rose-500/80 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">
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
                 SECTION 1: Snapshot Outlet
            ══════════════════════════════════════════════════ --}}
            <section class="app-animate-enter">
                <div class="app-panel relative overflow-hidden p-5 sm:p-6">
                    <div class="absolute inset-x-0 top-0 h-1 app-hero-gradient"></div>

                    <div class="flex items-start gap-4">
                        <div class="mt-0.5 flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold uppercase tracking-[0.14em] text-sky-600">Outlet</p>
                            <h3 class="mt-1 text-xl font-bold leading-tight text-slate-900 sm:text-2xl">{{ $outlet->name }}</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="app-badge {{ $statusColor }}">{{ $outlet->statusLabel() }}</span>
                                <span class="app-badge app-badge-sky">{{ $categoryLabel }}</span>
                                @if ($outlet->official_kode)
                                    <span class="app-badge app-badge-slate">{{ $outlet->official_kode }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Info rows --}}
                    <div class="mt-5 divide-y divide-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-4 px-5 py-3.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Cabang</p>
                                <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $outlet->branch?->name ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 px-5 py-3.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Alamat</p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-900">{{ $outlet->address ?: '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 px-5 py-3.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Dibuat oleh</p>
                                <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $outlet->creator?->name ?? '-' }}</p>
                            </div>
                        </div>

                        @if ($outlet->pic_name || $outlet->pic_phone)
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">PIC / Penanggung Jawab</p>
                                    <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $outlet->pic_name ?: '-' }}</p>
                                    @if ($outlet->pic_phone)
                                        <p class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                                            <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $outlet->pic_phone }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if ($outlet->verifier)
                            <div class="flex items-center gap-4 px-5 py-3.5">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Diverifikasi oleh</p>
                                    <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $outlet->verifier->name }}</p>
                                    @if ($outlet->verified_at)
                                        <p class="text-xs text-slate-500">{{ $outlet->verified_at->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 SECTION 2: KPI Stats
            ══════════════════════════════════════════════════ --}}
            <section class="app-animate-enter">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="app-kpi-blue">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-blue-600">Total Visit</p>
                        <p class="mt-2 pl-2 text-xl font-bold text-slate-900 sm:mt-3 sm:text-2xl">{{ $stats['total_visits'] }}</p>
                    </div>
                    <div class="app-kpi-sky">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-sky-600">Total Sales</p>
                        <p class="mt-2 pl-2 text-base font-bold text-slate-900 sm:mt-3 sm:text-lg">Rp {{ number_format($stats['total_sales'], 0, ',', '.') }}</p>
                    </div>
                    <div class="app-kpi-emerald">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-emerald-600">Collection</p>
                        <p class="mt-2 pl-2 text-base font-bold text-slate-900 sm:mt-3 sm:text-lg">Rp {{ number_format($stats['total_collection'], 0, ',', '.') }}</p>
                    </div>
                    <div class="app-kpi-amber">
                        <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] text-amber-600">Kunjungan Terakhir</p>
                        <p class="mt-2 pl-2 text-sm font-bold text-slate-900 sm:mt-3 sm:text-base">{{ $lastVisit?->visitedAtForBranch()?->format('d M Y') ?? 'Belum ada' }}</p>
                    </div>
                </div>
            </section>

            {{-- ═══════════════════════════════════════════════
                 SECTION 3: Map (from last visit coordinates)
            ══════════════════════════════════════════════════ --}}
            @if ($hasCoordinates)
                <section class="app-animate-enter">
                    <div class="app-panel overflow-hidden">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <h3 class="text-sm font-bold text-slate-800">Lokasi (Kunjungan Terakhir)</h3>
                            </div>
                            <a href="{{ 'https://www.google.com/maps?q='.$latitude.','.$longitude }}" target="_blank" rel="noopener noreferrer" class="app-btn-sm-primary gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                Google Maps
                            </a>
                        </div>
                        <iframe
                            title="Peta lokasi outlet"
                            src="{{ 'https://www.openstreetmap.org/export/embed.html?bbox='.$bbox.'&layer=mapnik&marker='.$latitude.','.$longitude }}"
                            class="h-[17rem] w-full border-0 sm:h-[20rem]"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                        <div class="border-t border-slate-100 px-5 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2 text-sm text-slate-500">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span class="font-mono text-xs font-semibold text-slate-700">{{ $latitude }}, {{ $longitude }}</span>
                                </div>
                                <a href="{{ 'https://www.openstreetmap.org/?mlat='.$latitude.'&mlon='.$longitude.'#map=18/'.$latitude.'/'.$longitude }}" target="_blank" rel="noopener noreferrer" class="app-btn-sm gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                    OpenStreetMap
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- ═══════════════════════════════════════════════
                 SECTION 4: Timeline Kunjungan
            ══════════════════════════════════════════════════ --}}
            <section class="app-panel app-animate-enter rounded-xl p-4 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Timeline</p>
                        <h3 class="app-section-title mt-2">Riwayat Kunjungan ke Outlet Ini</h3>
                    </div>
                    <span class="app-chip">{{ $visits->total() }} kunjungan</span>
                </div>

                {{-- Desktop Table --}}
                <div class="app-table-shell mt-5 hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3.5 font-semibold">Waktu</th>
                                <th class="px-4 py-3.5 font-semibold">User</th>
                                <th class="px-4 py-3.5 font-semibold">Tipe</th>
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
                                    <td class="px-4 py-3.5">
                                        <span class="app-badge {{ $visit->visit_type === 'sales' ? 'app-badge-sky' : 'app-badge-violet' }}">{{ $visit->typeLabel() }}</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5 text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5">
                                        <a href="{{ route('visit-history.show', $visit) }}" class="app-btn-sm">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-0">
                                        <div class="app-empty-state m-4">Belum ada kunjungan ke outlet ini.</div>
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
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-slate-900">{{ $visit->user?->name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</p>
                                </div>
                                <span class="app-badge shrink-0 {{ $visit->visit_type === 'sales' ? 'app-badge-sky' : 'app-badge-violet' }}">{{ $visit->typeLabel() }}</span>
                            </div>
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
                            <div class="mt-3 flex justify-end">
                                <a href="{{ route('visit-history.show', $visit) }}" class="app-btn-sm-primary">Lihat Detail</a>
                            </div>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada kunjungan ke outlet ini.</div>
                    @endforelse
                </div>

                @if ($visits->hasPages())
                    <div class="mt-5">{{ $visits->links() }}</div>
                @endif
            </section>

        </div>
    </div>

    @if (session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition
             class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-lg">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
             class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Delete Outlet Modal --}}
    @if (auth()->user()->canDeleteOutlets())
        <div id="deleteOutletModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Hapus Outlet?</h3>
                        <p class="mt-1 text-sm text-slate-500">Outlet "{{ $outlet->name }}" beserta data audit (status history, verification logs) akan dihapus permanen. Outlet yang sudah memiliki kunjungan tidak bisa dihapus.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('deleteOutletModal').classList.add('hidden')" class="app-action-secondary">Batal</button>
                    <form method="POST" action="{{ route('outlets.destroy', $outlet) }}">
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
