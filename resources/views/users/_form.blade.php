<div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]" x-data="{ role: '{{ old('role', $managedUser->role ?: \App\Models\User::ROLE_SALES) }}' }">
    <section class="space-y-6">
        <div class="app-soft-panel p-5">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="name" value="Nama user" />
                    <x-text-input id="name" name="name" class="mt-2 block w-full" :value="old('name', $managedUser->name)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label for="username" value="Username" />
                    <x-text-input id="username" name="username" class="mt-2 block w-full" :value="old('username', $managedUser->username)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>
                <div>
                    <x-input-label for="email" value="Email (opsional)" />
                    <x-text-input id="email" name="email" type="email" class="mt-2 block w-full" :value="old('email', $managedUser->email)" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
                <div>
                    <x-input-label for="role" value="Role" />
                    <select id="role" name="role" x-model="role" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('role')" />
                </div>
                <div class="sm:col-span-2" x-show="role !== '{{ \App\Models\User::ROLE_ADMIN_PUSAT }}'" x-cloak>
                    <x-input-label for="branch_id" value="Cabang" />
                    <select id="branch_id" name="branch_id" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        <option value="">Pilih cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((int) old('branch_id', $managedUser->branch_id) === $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('branch_id')" />
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="app-soft-panel p-5">
            <div class="grid gap-5">
                <div>
                    <x-input-label for="is_active" value="Status user" />
                    <select id="is_active" name="is_active" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                        <option value="1" @selected((string) old('is_active', $managedUser->is_active ?? true) === '1')>Active</option>
                        <option value="0" @selected((string) old('is_active', $managedUser->is_active ?? true) === '0')>Inactive</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                </div>
                <div>
                    <x-input-label for="password" :value="$managedUser->exists ? 'Password Baru (opsional)' : 'Password'" />
                    <x-text-input id="password" name="password" type="password" class="mt-2 block w-full" />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full" />
                </div>
            </div>
        </div>
    </section>
</div>

<div class="mt-6 flex flex-wrap gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Kembali</a>
</div>
