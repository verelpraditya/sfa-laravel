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
        <div class="min-h-screen overflow-hidden bg-[radial-gradient(circle_at_top_left,rgba(103,232,249,0.2),transparent_20%),radial-gradient(circle_at_top_right,rgba(37,99,235,0.18),transparent_28%),linear-gradient(180deg,#f8fbff_0%,#f1f6fc_48%,#eaf1f9_100%)]">
            <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col justify-center px-5 py-8 sm:px-6 lg:px-8">
                <div class="pointer-events-none absolute left-0 top-12 hidden h-56 w-56 rounded-full bg-sky-300/20 blur-3xl lg:block"></div>
                <div class="pointer-events-none absolute bottom-12 right-0 hidden h-72 w-72 rounded-full bg-blue-300/18 blur-3xl lg:block"></div>

                <div class="relative grid items-center gap-6 lg:grid-cols-[1fr_30rem] xl:grid-cols-[1.05fr_32rem]">
                    <div class="hidden lg:block">
                        <div class="max-w-2xl space-y-8">
                            <div class="inline-flex items-center gap-2 rounded-full border border-white/70 bg-white/82 px-4 py-2 text-xs font-semibold tracking-[0.22em] text-sky-700 shadow-sm backdrop-blur">
                                SFA DISTRIBUTOR PLATFORM
                            </div>

                            <div class="space-y-5">
                                <h1 class="max-w-3xl text-5xl font-semibold leading-[1.08] text-ink-950 xl:text-[3.7rem]">
                                    Login cepat untuk operasional sales yang rapi, ringan, dan siap dipakai di lapangan.
                                </h1>
                                <p class="max-w-2xl text-lg leading-8 text-slate-600">
                                    Satu workspace untuk sales, supervisor, SMD, dan admin memantau kunjungan, outlet, dan laporan dengan flow yang konsisten.
                                </p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-3">
                                <div class="rounded-[2rem] border border-white/70 bg-white/84 p-5 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.35)] backdrop-blur">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Akses</p>
                                    <p class="mt-3 text-2xl font-semibold text-ink-950">Username</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Masuk cepat tanpa email untuk kebutuhan operasional cabang.</p>
                                </div>
                                <div class="rounded-[2rem] border border-white/70 bg-white/84 p-5 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.35)] backdrop-blur">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Flow</p>
                                    <p class="mt-3 text-2xl font-semibold text-ink-950">Mobile First</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Nyaman dipakai dari HP saat kunjungan maupun dari desktop saat monitoring.</p>
                                </div>
                                <div class="rounded-[2rem] border border-white/70 bg-white/84 p-5 shadow-[0_20px_60px_-40px_rgba(15,23,42,0.35)] backdrop-blur">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Fokus</p>
                                    <p class="mt-3 text-2xl font-semibold text-ink-950">Lapangan</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Siap untuk kunjungan, verifikasi outlet, dan tindak lanjut performa harian.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mx-auto w-full max-w-md lg:max-w-none">
                        <div class="overflow-hidden rounded-[2.2rem] border border-white/75 bg-white/88 shadow-[0_34px_90px_-42px_rgba(15,23,42,0.45)] backdrop-blur-xl">
                            <div class="border-b border-slate-100/90 bg-[linear-gradient(180deg,rgba(248,251,255,0.95),rgba(241,247,255,0.82))] px-5 py-5 sm:px-7">
                                <div class="flex items-center gap-4">
                                    <a href="{{ url('/') }}" class="flex h-14 w-14 items-center justify-center rounded-[1.45rem] bg-[radial-gradient(circle_at_top,_#67e8f9,_#38bdf8_42%,_#2563eb_100%)] text-xl font-black text-white shadow-[0_18px_40px_-18px_rgba(56,189,248,0.9)]">
                                        S
                                    </a>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-700">SFA Distributor</p>
                                        <p class="mt-1 text-sm text-slate-500">Sales force automation workspace</p>
                                    </div>
                                </div>
                            </div>

                            <div class="px-5 py-6 sm:px-7 sm:py-7">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
