@php($user = auth()->user())

<div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
    <section class="space-y-6">
        <div class="app-soft-panel p-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="name" value="Nama outlet" />
                    <x-text-input id="name" name="name" class="mt-2 block w-full" :value="old('name', $outlet->name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="pic_name" value="Nama PIC (opsional)" />
                    <x-text-input id="pic_name" name="pic_name" class="mt-2 block w-full" :value="old('pic_name', $outlet->pic_name)" placeholder="Nama pemilik / penanggung jawab" />
                    <x-input-error class="mt-2" :messages="$errors->get('pic_name')" />
                </div>

                <div>
                    <x-input-label for="pic_phone" value="No. Telepon PIC (opsional)" />
                    <x-text-input id="pic_phone" name="pic_phone" class="mt-2 block w-full" :value="old('pic_phone', $outlet->pic_phone)" placeholder="08xxxxxxxxxx" inputmode="numeric" />
                    <x-input-error class="mt-2" :messages="$errors->get('pic_phone')" />
                </div>

                @if ($user->isAdminPusat())
                    <div class="sm:col-span-2">
                        <x-input-label for="branch_id" value="Cabang" />
                        <select id="branch_id" name="branch_id" class="app-select mt-2 block w-full">
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
                    <textarea id="address" name="address" rows="4" class="app-textarea mt-2 block w-full">{{ old('address', $outlet->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-6" x-data="{ outletStatus: '{{ old('outlet_status', $outlet->outlet_status ?: 'prospek') }}' }">
        <div class="app-soft-panel p-5">
            <div class="grid gap-5">
                <div>
                    <x-input-label for="category" value="Kategori outlet" />
                    <select id="category" name="category" class="app-select mt-2 block w-full">
                        @foreach (['salon' => 'Salon', 'toko' => 'Toko', 'barbershop' => 'Barbershop', 'lainnya' => 'Lainnya'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('category', $outlet->category) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('category')" />
                </div>

                <div>
                    <x-input-label for="outlet_status" value="Status outlet" />
                    <select id="outlet_status" name="outlet_status" x-model="outletStatus" class="app-select mt-2 block w-full">
                        <option value="prospek" @selected(old('outlet_status', $outlet->outlet_status ?: 'prospek') === 'prospek')>Prospek</option>
                        <option value="pending" @selected(old('outlet_status', $outlet->outlet_status) === 'pending')>Pending</option>
                        <option value="active" @selected(old('outlet_status', $outlet->outlet_status) === 'active')>Aktif</option>
                        <option value="inactive" @selected(old('outlet_status', $outlet->outlet_status) === 'inactive')>Inactive</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('outlet_status')" />
                </div>

                <div x-cloak x-show="outletStatus === 'active'" x-transition>
                    <x-input-label for="official_kode" value="Official kode" />
                    <x-text-input id="official_kode" name="official_kode" class="mt-2 block w-full" :value="old('official_kode', $outlet->official_kode)" placeholder="Mis. OFF-001" oninput="this.value = this.value.replaceAll(' ', '').toUpperCase()" autocomplete="off" spellcheck="false" autocapitalize="characters" />
                    <x-input-error class="mt-2" :messages="$errors->get('official_kode')" />
                </div>
            </div>
        </div>

        <div class="app-soft-panel p-5 text-sm leading-7 text-slate-600">
            <p class="font-semibold text-slate-900">Catatan rule bisnis</p>
            <p class="mt-2">Gunakan `Prospek` untuk outlet yang masih tahap follow up, `Pending` untuk outlet yang masih menunggu official kode, `Aktif` untuk outlet yang sudah resmi aktif, dan `Inactive` untuk outlet yang sudah tidak berjalan.</p>
        </div>
    </section>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('outlets.index') }}" class="app-action-secondary px-5 py-3">Kembali</a>
</div>
