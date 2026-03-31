<x-guest-layout>
    <div class="space-y-6" x-data="{ showPassword: false }">
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="app-chip">Login</span>
                <span class="app-chip">Secure Access</span>
            </div>
            <h2 class="text-3xl font-semibold text-ink-950">Masuk ke dashboard</h2>
            <p class="text-sm leading-6 text-slate-500">Masukkan username dan password untuk masuk ke workspace sesuai role kamu.</p>
        </div>

        <x-auth-session-status class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="app-soft-panel p-4 sm:p-5">
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input
                    id="username"
                    class="mt-2 block w-full"
                    type="text"
                    name="username"
                    :value="old('username')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Mis. salesbdg"
                />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <div class="app-soft-panel p-4 sm:p-5">
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')" />

                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-brand-700 transition hover:text-brand-800" href="{{ route('password.request') }}">
                            {{ __('Lupa password?') }}
                        </a>
                    @endif
                </div>

                <div class="relative mt-2">
                    <x-text-input
                        id="password"
                        class="block w-full pr-24"
                        x-bind:type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                    />

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-3 my-auto rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-slate-700"
                    >
                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                    </button>
                </div>

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <label for="remember_me" class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" name="remember">
                <span>
                    <span class="block font-medium text-slate-700">{{ __('Tetap masuk') }}</span>
                    <span class="block text-xs text-slate-500">Cocok untuk device kerja pribadi yang aman.</span>
                </span>
            </label>

            <div class="space-y-3 pt-2">
                <x-primary-button class="w-full justify-center text-sm">
                    {{ __('Masuk ke Dashboard') }}
                </x-primary-button>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs leading-6 text-slate-500">
                    Pastikan login menggunakan akun operasional yang sudah dibuat admin. Jika ada kendala akses, hubungi admin pusat atau supervisor terkait.
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
