<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">Deteksi Duplikat</span>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">Outlet Duplikat</h2>
                    <p class="mt-1 text-sm text-white/70">Sistem mendeteksi outlet yang memiliki nama atau kode yang sama di cabang yang sama.</p>
                </div>
                <a href="{{ route('outlets.index') }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    Kembali ke Outlet
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            @if ($groups->isEmpty())
                <section class="app-panel app-animate-enter p-6 sm:p-8">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">Tidak ada duplikat terdeteksi</h3>
                        <p class="mt-1 text-sm text-slate-500">Semua outlet di cabang Anda sudah unik. Tidak ada tindakan yang diperlukan.</p>
                    </div>
                </section>
            @else
                <section class="app-panel app-animate-enter p-4 sm:p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="app-overline">Hasil Deteksi</p>
                            <h3 class="app-section-title mt-2">Grup Outlet yang Diduga Duplikat</h3>
                        </div>
                        <span class="app-chip">{{ $groups->count() }} grup</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        @foreach ($groups as $index => $group)
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm transition hover:shadow-md">
                                {{-- Group Header --}}
                                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="app-badge {{ $group['type'] === 'name' ? 'app-badge-amber' : 'app-badge-violet' }}">
                                                {{ $group['type'] === 'name' ? 'Nama Sama' : 'Kode Sama' }}
                                            </span>
                                            <span class="app-badge app-badge-slate">{{ $group['branch'] }}</span>
                                        </div>
                                        <p class="mt-2 text-sm font-bold text-slate-900">
                                            {{ $group['type'] === 'name' ? '"'.ucfirst($group['key']).'"' : 'Kode: '.$group['key'] }}
                                        </p>
                                    </div>
                                    <span class="shrink-0 text-sm font-semibold text-slate-500">{{ $group['outlets']->count() }} outlet</span>
                                </div>

                                {{-- Outlets in this group --}}
                                <div class="divide-y divide-slate-50 px-5">
                                    @foreach ($group['outlets'] as $outlet)
                                        <div class="flex items-center justify-between gap-3 py-3.5">
                                            <div class="min-w-0">
                                                <p class="truncate font-semibold text-slate-900">{{ $outlet->name }}</p>
                                                <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-500">
                                                    <span>{{ $outlet->district }}, {{ $outlet->city }}</span>
                                                    <span>Kode: {{ $outlet->official_kode ?: '-' }}</span>
                                                    <span>{{ $outlet->visits->count() }} kunjungan</span>
                                                </div>
                                            </div>
                                            @php $statusColor = match($outlet->outlet_status) {
                                                'active' => 'app-badge-emerald', 'pending' => 'app-badge-amber',
                                                'prospek' => 'app-badge-violet', 'inactive' => 'app-badge-rose', default => 'app-badge-slate',
                                            }; @endphp
                                            <span class="app-badge shrink-0 {{ $statusColor }}">{{ $outlet->statusLabel() }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Action --}}
                                <div class="flex items-center justify-end border-t border-slate-100 px-5 py-3">
                                    <a href="{{ route('outlets.merge', $group['outlets']->first()) }}" class="app-btn-sm-primary gap-1.5">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        Bandingkan & Gabungkan
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

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
</x-app-layout>
