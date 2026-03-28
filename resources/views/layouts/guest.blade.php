<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SFA Distributor') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(37,99,235,0.16),_transparent_34%),linear-gradient(180deg,#f8fbff_0%,#f4f7fb_50%,#eef3f8_100%)]">
            <div class="mx-auto flex min-h-screen max-w-6xl flex-col justify-center px-5 py-8 sm:px-6 lg:px-8">
                <div class="grid items-center gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div class="hidden lg:block">
                        <div class="max-w-xl space-y-6">
                            <div class="inline-flex items-center gap-2 rounded-full border border-brand-100 bg-white/80 px-4 py-2 text-xs font-semibold tracking-[0.18em] text-brand-700 shadow-sm backdrop-blur">
                                MOBILE-FIRST SFA PLATFORM
                            </div>
                            <div class="space-y-4">
                                <h1 class="text-5xl font-semibold leading-tight text-ink-950">
                                    Login cepat untuk tim sales, supervisor, dan SMD.
                                </h1>
                                <p class="text-lg leading-8 text-slate-600">
                                    Sistem SFA distributor yang ringan, modern, dan siap dipakai di desktop maupun lapangan.
                                </p>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-3xl border border-white/80 bg-white/85 p-5 shadow-sm shadow-slate-200/60">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Auth</p>
                                    <p class="mt-3 text-xl font-semibold text-ink-950">Breeze Blade</p>
                                    <p class="mt-2 text-sm text-slate-500">Session auth yang ringan dan aman untuk shared hosting.</p>
                                </div>
                                <div class="rounded-3xl border border-white/80 bg-white/85 p-5 shadow-sm shadow-slate-200/60">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Login</p>
                                    <p class="mt-3 text-xl font-semibold text-ink-950">Username</p>
                                    <p class="mt-2 text-sm text-slate-500">Praktis untuk operasional cabang tanpa bergantung ke email.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mx-auto w-full max-w-md lg:max-w-lg">
                        <div class="rounded-[2rem] border border-white/80 bg-white/90 p-5 shadow-[0_30px_80px_-40px_rgba(15,23,42,0.42)] backdrop-blur sm:p-7">
                            <div class="mb-8 flex items-center gap-3">
                                <a href="{{ url('/') }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-600 text-lg font-bold text-white shadow-lg shadow-brand-600/20">
                                    S
                                </a>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700">SFA Distributor</p>
                                    <p class="mt-1 text-sm text-slate-500">Sales force automation for distributor teams</p>
                                </div>
                            </div>

                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
