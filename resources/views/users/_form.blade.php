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
                    <select id="role" name="role" x-model="role" class="app-select mt-2 block w-full">
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('role')" />
                </div>
                <div class="sm:col-span-2" x-show="role !== '{{ \App\Models\User::ROLE_ADMIN_PUSAT }}'" x-cloak>
                    <x-input-label for="branch_id" value="Cabang" />
                    <select id="branch_id" name="branch_id" class="app-select mt-2 block w-full">
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
                    <select id="is_active" name="is_active" class="app-select mt-2 block w-full">
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
    <a href="{{ route('users.index') }}" class="app-action-secondary px-5 py-3">Kembali</a>
</div>
