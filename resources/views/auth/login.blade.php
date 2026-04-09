<x-guest-layout>
    <div class="space-y-6" x-data="{ showPassword: false }">
        <div class="space-y-3">
            <h2 class="app-page-title">Masuk ke dashboard</h2>
            <p class="app-body-copy">Masukkan username dan password untuk masuk ke workspace sesuai role kamu.</p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div class="app-soft-panel p-4 sm:p-5">
                <x-input-label for="username" :value="__('Username')" />
                <div class="app-input-shell mt-2">
                    <span class="app-input-icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.25 18.25v-1a3.25 3.25 0 0 0-3.25-3.25H8.25A3.25 3.25 0 0 0 5 17.25v1" /><circle cx="10.125" cy="8.25" r="3.25" stroke-width="1.8" /></svg>
                    </span>
                    <x-text-input
                        id="username"
                        class="app-field-with-icon block w-full"
                        type="text"
                        name="username"
                        :value="old('username')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Mis. salesbdg"
                    />
                </div>
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

                <div class="app-input-shell mt-2">
                    <span class="app-input-icon">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="5.5" y="10.5" width="13" height="8" rx="2" stroke-width="1.8" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.5 10.5V8a3.5 3.5 0 1 1 7 0v2.5" /></svg>
                    </span>
                    <x-text-input
                        id="password"
                        class="app-field-with-icon block w-full pr-14"
                        x-bind:type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                    />

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        x-bind:aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                        class="absolute inset-y-0 right-3 z-10 my-auto inline-flex h-9 w-9 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100/80 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-100"
                    >
                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6-9.75-6-9.75-6Z" />
                            <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                        </svg>
                        <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.585 10.587A2 2 0 0 0 13.414 13.414" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.88 5.09A10.94 10.94 0 0 1 12 4.875c6 0 9.75 7.125 9.75 7.125a16.63 16.63 0 0 1-3.204 4.03" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.61 6.612C4.162 8.145 2.25 12 2.25 12s3.75 7.125 9.75 7.125a10.8 10.8 0 0 0 4.138-.788" />
                        </svg>
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

                <div class="app-alert app-alert-warning">
                    <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/80 text-amber-600 shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M12 8v5m0 3h.01" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" /></svg>
                    </span>
                    <div>
                        <p class="text-[12px] font-semibold uppercase tracking-[0.14em] text-amber-700">Info Login</p>
                        <p class="mt-1">Pastikan login menggunakan akun operasional yang sudah dibuat admin. Jika ada kendala akses, hubungi admin pusat atau supervisor terkait.</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
