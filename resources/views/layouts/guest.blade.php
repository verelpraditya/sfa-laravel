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
    <body class="text-slate-900 antialiased">
        <div class="min-h-screen bg-slate-50">
            <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col justify-center px-5 py-8 sm:px-6 lg:px-8">
                <div class="relative grid items-center gap-6 lg:grid-cols-[1fr_30rem] xl:grid-cols-[1.05fr_32rem]">
                    <div class="hidden lg:block">
                        <div class="max-w-2xl space-y-8">
                            <div class="inline-flex items-center gap-2 rounded-md border border-slate-200 bg-white px-4 py-2 text-[11px] font-semibold tracking-[0.16em] text-sky-600 shadow-sm">
                                SFA DISTRIBUTOR PLATFORM
                            </div>

                            <div class="space-y-5">
                                <h1 class="max-w-3xl text-[2rem] font-semibold leading-[1.08] tracking-[-0.03em] text-slate-900 xl:text-[3rem]">
                                    Login cepat untuk operasional sales yang rapi, ringan, dan siap dipakai di lapangan.
                                </h1>
                                <p class="max-w-2xl text-[14px] leading-7 text-slate-600 xl:text-base">
                                    Satu workspace untuk sales, supervisor, SMD, dan admin memantau kunjungan, outlet, dan laporan dengan flow yang konsisten.
                                </p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-3">
                                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Akses</p>
                                    <p class="mt-3 text-2xl font-semibold text-slate-900">Username</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Masuk cepat tanpa email untuk kebutuhan operasional cabang.</p>
                                </div>
                                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Flow</p>
                                    <p class="mt-3 text-2xl font-semibold text-slate-900">Mobile First</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Nyaman dipakai dari HP saat kunjungan maupun dari desktop saat monitoring.</p>
                                </div>
                                <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Fokus</p>
                                    <p class="mt-3 text-2xl font-semibold text-slate-900">Lapangan</p>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">Siap untuk kunjungan, verifikasi outlet, dan tindak lanjut performa harian.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mx-auto w-full max-w-md lg:max-w-none">
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm sm:rounded-xl">
                            <div class="border-b border-slate-100 bg-slate-50 px-5 py-5 sm:px-7">
                                <div class="flex items-center gap-4">
                                    <a href="{{ url('/') }}" class="flex h-14 w-14 items-center justify-center rounded-xl bg-sky-500 text-xl font-black text-white shadow-sm">
                                        S
                                    </a>
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-600">SFA Distributor</p>
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
