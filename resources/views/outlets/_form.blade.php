@php($user = auth()->user())

<div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
    <section class="space-y-6">
        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="name" value="Nama outlet" />
                    <x-text-input id="name" name="name" class="mt-2 block w-full" :value="old('name', $outlet->name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                @if ($user->isAdminPusat())
                    <div class="sm:col-span-2">
                        <x-input-label for="branch_id" value="Cabang" />
                        <select id="branch_id" name="branch_id" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                            <option value="">Pilih cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((int) old('branch_id', $outlet->branch_id) === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('branch_id')" />
                    </div>
                @endif

                <div>
                    <x-input-label for="district" value="Kecamatan" />
                    <x-text-input id="district" name="district" class="mt-2 block w-full" :value="old('district', $outlet->district)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('district')" />
                </div>

                <div>
                    <x-input-label for="city" value="Kota" />
                    <x-text-input id="city" name="city" class="mt-2 block w-full" :value="old('city', $outlet->city)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>

                <div class="sm:col-span-2">
                    <x-input-label for="address" value="Alamat outlet" />
                    <textarea id="address" name="address" rows="4" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 outline-none transition placeholder:text-slate-400 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">{{ old('address', $outlet->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-6" x-data="{ outletType: '{{ old('outlet_type', $outlet->outlet_type ?: 'prospek') }}' }">
        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
            <div class="grid gap-5">
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

                <div x-show="outletType === 'pelanggan_lama'" x-transition>
                    <x-input-label for="official_kode" value="Official kode" />
                    <x-text-input id="official_kode" name="official_kode" class="mt-2 block w-full" :value="old('official_kode', $outlet->official_kode)" placeholder="Mis. OFF-001" />
                    <x-input-error class="mt-2" :messages="$errors->get('official_kode')" />
                </div>

                <div>
                    <x-input-label for="verification_status" value="Status verifikasi" />
                    <select id="verification_status" name="verification_status" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                        <option value="pending" @selected(old('verification_status', $outlet->verification_status ?: 'pending') === 'pending')>Pending</option>
                        <option value="verified" @selected(old('verification_status', $outlet->verification_status) === 'verified')>Verified</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('verification_status')" />
                </div>
            </div>
        </div>

        <div class="rounded-[1.5rem] border border-slate-200 bg-brand-50/60 p-5 text-sm leading-7 text-slate-600">
            <p class="font-semibold text-ink-950">Catatan rule bisnis</p>
            <p class="mt-2">`Pelanggan Lama` wajib punya `official_kode`. Untuk `Prospek` dan `NOO`, field official kode tetap bisa kosong sampai diverifikasi supervisor.</p>
        </div>
    </section>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('outlets.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-200/60 transition hover:border-slate-300 hover:bg-slate-50">Kembali</a>
</div>
