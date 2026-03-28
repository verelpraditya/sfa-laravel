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
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('status') }}</div>
            @endif

            <div class="grid gap-6 xl:grid-cols-[1fr_0.92fr]">
                <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60 sm:p-6">
                    <form method="POST" action="{{ route('outlet-verifications.update', $outlet) }}" class="space-y-6" x-data="{ outletType: '{{ old('outlet_type', $outlet->outlet_type) }}' }">
                        @csrf
                        @method('PUT')

                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
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
                                <x-input-label for="category" value="Kategori outlet" />
                                <select id="category" name="category" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                                    @foreach (['salon' => 'Salon', 'toko' => 'Toko', 'barbershop' => 'Barbershop', 'lainnya' => 'Lainnya'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('category', $outlet->category) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category')" />
                            </div>

                            <div>
                                <x-input-label for="outlet_type" value="Jenis outlet" />
                                <select id="outlet_type" name="outlet_type" x-model="outletType" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                                    <option value="prospek">Prospek</option>
                                    <option value="noo">NOO</option>
                                    <option value="pelanggan_lama">Pelanggan Lama</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('outlet_type')" />
                            </div>

                            <div>
                                <x-input-label for="verification_status" value="Status verifikasi" />
                                <select id="verification_status" name="verification_status" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                                    <option value="">Tidak Perlu</option>
                                    <option value="pending" @selected(old('verification_status', $outlet->verification_status) === 'pending')>Pending</option>
                                    <option value="verified" @selected(old('verification_status', $outlet->verification_status) === 'verified')>Verified</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('verification_status')" />
                            </div>

                            <div>
                                <x-input-label for="outlet_status" value="Status outlet" />
                                <select id="outlet_status" name="outlet_status" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                                    <option value="active" @selected(old('outlet_status', $outlet->outlet_status) === 'active')>Active</option>
                                    <option value="inactive" @selected(old('outlet_status', $outlet->outlet_status) === 'inactive')>Inactive</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('outlet_status')" />
                            </div>

                            <div class="md:col-span-2" x-cloak x-show="['noo', 'pelanggan_lama'].includes(outletType)" x-transition>
                                <x-input-label for="official_kode" value="Official kode" />
                                <x-text-input id="official_kode" name="official_kode" class="mt-2 block w-full" :value="old('official_kode', $outlet->official_kode)" placeholder="Mis. OFF-BDG-010" />
                                <x-input-error class="mt-2" :messages="$errors->get('official_kode')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="verification_notes" value="Catatan verifikasi" />
                                <textarea id="verification_notes" name="verification_notes" rows="4" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">{{ old('verification_notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('verification_notes')" />
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <x-primary-button>Simpan Verifikasi</x-primary-button>
                        </div>
                    </form>
                </section>

                <section class="space-y-6">
                    <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ringkasan Outlet</p>
                        <div class="mt-4 space-y-3 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            <p><span class="font-semibold text-slate-900">Dibuat oleh:</span> {{ $outlet->creator?->name ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Status outlet:</span> {{ $outlet->statusLabel() }}</p>
                            <p><span class="font-semibold text-slate-900">Status verifikasi:</span> {{ $outlet->verificationLabel() }}</p>
                            <p><span class="font-semibold text-slate-900">Verifier terakhir:</span> {{ $outlet->verifier?->name ?? '-' }}</p>
                            <p><span class="font-semibold text-slate-900">Verified at:</span> {{ $outlet->verified_at?->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Riwayat Kunjungan Terkait</p>
                        <div class="mt-4 space-y-3">
                            @forelse ($recentVisits as $visit)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
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
