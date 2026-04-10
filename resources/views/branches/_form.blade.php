<div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
    <section class="space-y-6">
        <div class="app-soft-panel p-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="code" value="Kode cabang" />
                    <x-text-input id="code" name="code" class="mt-2 block w-full" :value="old('code', $branch->code)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('code')" />
                </div>
                <div>
                    <x-input-label for="name" value="Nama cabang" />
                    <x-text-input id="name" name="name" class="mt-2 block w-full" :value="old('name', $branch->name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label for="city" value="Kota" />
                    <x-text-input id="city" name="city" class="mt-2 block w-full" :value="old('city', $branch->city)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                </div>
                <div>
                    <x-input-label for="timezone" value="Zona waktu" />
                    <select id="timezone" name="timezone" class="app-select mt-2 block w-full">
                        @foreach ($timezones as $value => $label)
                            <option value="{{ $value }}" @selected(old('timezone', $branch->timezone ?: 'Asia/Jakarta') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                </div>
                <div class="sm:col-span-2">
                    <x-input-label for="address" value="Alamat cabang" />
                    <textarea id="address" name="address" rows="4" class="app-textarea mt-2 block w-full">{{ old('address', $branch->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="app-soft-panel p-5">
            <div>
                <x-input-label for="is_active" value="Status cabang" />
                <select id="is_active" name="is_active" class="app-select mt-2 block w-full">
                    <option value="1" @selected((string) old('is_active', $branch->is_active ?? true) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', $branch->is_active ?? true) === '0')>Inactive</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
            </div>
        </div>

        <div class="app-soft-panel p-5 text-sm leading-7 text-slate-600">
            <p class="font-semibold text-slate-900">Catatan timezone</p>
            <p class="mt-2">Zona waktu cabang dipakai untuk menampilkan jam kunjungan sesuai kota cabang, misalnya `Asia/Jakarta` untuk WIB dan `Asia/Makassar` untuk WITA.</p>
        </div>
    </section>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('branches.index') }}" class="app-action-secondary px-5 py-3">Kembali</a>
</div>
