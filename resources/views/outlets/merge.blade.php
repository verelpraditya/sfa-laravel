@php
    $statusColor = static fn ($status) => match($status) {
        'active'   => 'app-badge-emerald',
        'pending'  => 'app-badge-amber',
        'prospek'  => 'app-badge-violet',
        'inactive' => 'app-badge-rose',
        default    => 'app-badge-slate',
    };
    $categoryLabel = static fn ($cat) => match($cat) {
        'salon'      => 'Salon',
        'toko'       => 'Toko',
        'barbershop' => 'Barbershop',
        'lainnya'    => 'Lainnya',
        default      => '-',
    };
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold text-white">Merge Outlet</span>
                    </div>
                    <h2 class="mt-2 text-2xl font-bold text-white sm:text-3xl">Gabungkan Outlet Duplikat</h2>
                    <p class="mt-1 text-sm text-white/70">Bandingkan data outlet, pilih master, lalu gabungkan.</p>
                </div>
                <a href="{{ route('outlets.duplicates') }}" class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-white/15 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/25">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7" x-data="mergeForm()">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">

            {{-- Instructions --}}
            <section class="app-panel app-animate-enter border-l-4 border-sky-400 p-4 sm:p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Cara Merge</h3>
                        <ol class="mt-2 list-inside list-decimal space-y-1 text-sm text-slate-600">
                            <li>Pilih satu outlet sebagai <strong>master</strong> (data yang dipertahankan)</li>
                            <li>Outlet lain akan ditandai sebagai duplikat</li>
                            <li>Semua kunjungan duplikat dipindahkan ke master</li>
                            <li>Outlet duplikat dihapus setelah merge</li>
                        </ol>
                    </div>
                </div>
            </section>

            {{-- Side-by-side comparison --}}
            <section class="app-panel app-animate-enter p-4 sm:p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Perbandingan</p>
                        <h3 class="app-section-title mt-2">{{ $allOutlets->count() }} Outlet Serupa</h3>
                    </div>
                </div>

                <div class="mt-5 grid gap-4 {{ $allOutlets->count() === 2 ? 'lg:grid-cols-2' : 'lg:grid-cols-3' }}">
                    @foreach ($allOutlets as $outlet)
                        <div
                            class="relative rounded-xl border-2 p-5 shadow-sm transition-all duration-200 cursor-pointer"
                            :class="masterId == {{ $outlet->id }}
                                ? 'border-sky-400 bg-sky-50/50 ring-2 ring-sky-200 shadow-md'
                                : (duplicateIds.includes({{ $outlet->id }})
                                    ? 'border-rose-300 bg-rose-50/30'
                                    : 'border-slate-200 bg-white hover:border-slate-300')"
                            @click="selectMaster({{ $outlet->id }})"
                        >
                            {{-- Master / Duplicate badge --}}
                            <div class="absolute -top-3 right-4">
                                <span x-show="masterId == {{ $outlet->id }}" x-cloak class="inline-flex items-center gap-1 rounded-full bg-sky-500 px-3 py-1 text-xs font-bold text-white shadow">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Master
                                </span>
                                <span x-show="duplicateIds.includes({{ $outlet->id }})" x-cloak class="inline-flex items-center gap-1 rounded-full bg-rose-500 px-3 py-1 text-xs font-bold text-white shadow">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Duplikat
                                </span>
                            </div>

                            {{-- Outlet Info --}}
                            <div class="mt-2">
                                <h4 class="text-lg font-bold text-slate-900">{{ $outlet->name }}</h4>
                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="app-badge {{ $statusColor($outlet->outlet_status) }}">{{ $outlet->statusLabel() }}</span>
                                    <span class="app-badge app-badge-sky">{{ $categoryLabel($outlet->category) }}</span>
                                </div>
                            </div>

                            <div class="mt-4 space-y-2.5 text-sm">
                                <div class="flex items-start gap-2">
                                    <span class="w-28 shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Official Kode</span>
                                    <span class="font-medium text-slate-900">{{ $outlet->official_kode ?: '-' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-28 shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</span>
                                    <span class="font-medium text-slate-700">{{ $outlet->address ?: '-' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-28 shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Kecamatan</span>
                                    <span class="font-medium text-slate-700">{{ $outlet->district ?: '-' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-28 shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Kota</span>
                                    <span class="font-medium text-slate-700">{{ $outlet->city ?: '-' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-28 shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Cabang</span>
                                    <span class="font-medium text-slate-700">{{ $outlet->branch?->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-28 shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Dibuat oleh</span>
                                    <span class="font-medium text-slate-700">{{ $outlet->creator?->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="w-28 shrink-0 text-xs font-semibold uppercase tracking-wide text-slate-400">Diverifikasi</span>
                                    <span class="font-medium text-slate-700">{{ $outlet->verifier?->name ?? 'Belum' }}</span>
                                </div>
                            </div>

                            {{-- Visit count KPI --}}
                            <div class="mt-4 rounded-lg border border-slate-100 bg-slate-50 px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400">Total Kunjungan</p>
                                <p class="mt-1 text-xl font-bold text-slate-900">{{ $outlet->visits_count ?? 0 }}</p>
                            </div>

                            {{-- Click hint --}}
                            <p class="mt-3 text-center text-xs text-slate-400" x-show="!masterId">Klik untuk pilih sebagai master</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Action bar --}}
            <section class="app-panel app-animate-enter p-4 sm:p-5" x-show="masterId" x-cloak>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Konfirmasi Merge</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            <span x-text="duplicateIds.length"></span> outlet duplikat akan dihapus.
                            Semua kunjungan akan dipindahkan ke outlet master.
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="masterId = null; duplicateIds = []" class="app-action-secondary px-4 py-2.5 text-sm">
                            Reset Pilihan
                        </button>
                        <button type="button" @click="confirmMerge = true" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-600 focus:ring-2 focus:ring-sky-300 focus:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Gabungkan Sekarang
                        </button>
                    </div>
                </div>
            </section>

        </div>

        {{-- Merge Confirmation Modal --}}
        <div x-show="confirmMerge" x-cloak x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div @click.away="confirmMerge = false" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Gabung Outlet</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            <span x-text="duplicateIds.length"></span> outlet duplikat akan dihapus dan semua datanya dipindahkan ke master. Tindakan ini tidak bisa dibatalkan.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="confirmMerge = false" class="app-action-secondary">Batal</button>
                    <form :action="'{{ url('outlets/duplicates') }}/' + masterId + '/merge'" method="POST" x-ref="mergeForm">
                        @csrf
                        <input type="hidden" name="master_id" :value="masterId">
                        <template x-for="id in duplicateIds" :key="id">
                            <input type="hidden" name="duplicate_ids[]" :value="id">
                        </template>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-600">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Ya, Gabungkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
             class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-full bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <script>
        function mergeForm() {
            const allIds = @json($allOutlets->pluck('id')->values());
            return {
                masterId: null,
                duplicateIds: [],
                confirmMerge: false,
                selectMaster(id) {
                    this.masterId = id;
                    this.duplicateIds = allIds.filter(i => i !== id);
                },
            };
        }
    </script>
</x-app-layout>
