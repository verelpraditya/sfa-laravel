<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Supervisor Workflow</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Verifikasi Outlet</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $outlet->name }} · {{ $outlet->branch?->name }}</p>
            </div>
            <a href="{{ route('outlet-verifications.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-200/60 transition hover:border-slate-300 hover:bg-slate-50">Kembali ke daftar</a>
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

            <div class="grid gap-6 xl:grid-cols-[1fr_0.92fr]">
                <section class="app-panel overflow-hidden p-0 sm:p-0">
                    <div class="bg-[linear-gradient(135deg,#0f172a_0%,#1d4ed8_100%)] px-5 py-5 text-white sm:px-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-200/80">Verification Panel</p>
                        <h3 class="mt-2 text-xl font-semibold">Aktifkan outlet pending</h3>
                        <p class="mt-2 max-w-2xl text-sm leading-7 text-sky-100/85">Isi official kode untuk mengaktifkan outlet. Data customer di halaman ini hanya ditampilkan sebagai referensi.</p>
                    </div>
                    <div class="p-5 sm:p-6">
                    <form method="POST" action="{{ route('outlet-verifications.update', $outlet) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="app-soft-panel p-5">
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <x-input-label value="Nama outlet" />
                                    <div class="mt-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">{{ $outlet->name }}</div>
                                </div>
                                <div>
                                    <x-input-label value="Cabang" />
                                    <div class="mt-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">{{ $outlet->branch?->name }}</div>
                                </div>
                                <div>
                                    <x-input-label value="Kecamatan" />
                                    <div class="mt-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">{{ $outlet->district }}</div>
                                </div>
                                <div>
                                    <x-input-label value="Kota" />
                                    <div class="mt-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">{{ $outlet->city }}</div>
                                </div>
                                <div class="sm:col-span-2">
                                    <x-input-label value="Alamat" />
                                    <div class="mt-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">{{ $outlet->address }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <x-input-label value="Kategori outlet" />
                                <div class="mt-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700">{{ ucfirst($outlet->category) }}</div>
                            </div>

                            <div>
                                <x-input-label value="Status outlet" />
                                <div class="mt-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-700">{{ $outlet->statusLabel() }}</div>
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="official_kode" value="Official kode" />
                                <x-text-input id="official_kode" name="official_kode" class="mt-2 block w-full" :value="old('official_kode', $outlet->official_kode)" placeholder="Mis. OFF-BDG-010" oninput="this.value = this.value.replaceAll(' ', '').toUpperCase()" autocomplete="off" spellcheck="false" autocapitalize="characters" />
                                <x-input-error class="mt-2" :messages="$errors->get('official_kode')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="verification_notes" value="Catatan verifikasi" />
                                <textarea id="verification_notes" name="verification_notes" rows="4" class="app-textarea mt-2 block w-full">{{ old('verification_notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('verification_notes')" />
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <x-primary-button>Simpan Verifikasi</x-primary-button>
                        </div>
                    </form>
                    </div>
                </section>

                <section class="space-y-6">
                    <div class="app-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ringkasan Outlet</p>
                        <div class="app-soft-panel mt-4 space-y-3 p-4 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Dibuat oleh:</span> {{ $outlet->creator?->name ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Status outlet:</span> {{ $outlet->statusLabel() }}</p>
                            <p><span class="font-semibold text-slate-900">Official Kode:</span> {{ $outlet->official_kode ?: '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Verifier terakhir:</span> {{ $outlet->verifier?->name ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Verified at:</span> {{ $outlet->verified_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="app-panel p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Riwayat Kunjungan Terkait</p>
                        <div class="mt-4 space-y-3">
                            @forelse ($recentVisits as $visit)
                                <div class="app-soft-panel px-4 py-3 text-sm text-slate-600">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-slate-900">{{ strtoupper($visit->visit_type) }} · {{ $visit->user?->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $visit->visited_at?->format('d M Y H:i') }}</p>
                                    </div>
                                    <p class="mt-2">{{ $visit->notes ?: 'Tanpa catatan tambahan.' }}</p>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Belum ada riwayat kunjungan untuk outlet ini.</div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
