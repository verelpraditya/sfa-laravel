<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'SFA Distributor') }}</title>
        <meta
            name="description"
            content="SFA distributor berbasis web untuk kunjungan outlet, pertumbuhan master outlet, dan monitoring performa cabang."
        >

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body>
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(37,99,235,0.14),_transparent_38%),linear-gradient(180deg,#f8fbff_0%,#f5f7fb_48%,#eef3f8_100%)]">
            <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-5 py-6 sm:px-6 lg:px-8">
                <header class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-lg font-bold text-white shadow-lg shadow-brand-600/20">
                            S
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-700">SFA Distributor</p>
                            <h1 class="text-sm font-medium text-slate-500">Mobile-first sales force automation</h1>
                        </div>
                    </div>
                    <div class="rounded-full border border-white/70 bg-white/80 px-4 py-2 text-xs font-medium text-slate-500 shadow-sm backdrop-blur">
                        Laravel + Alpine + Chart.js
                    </div>
                </header>

                <main class="grid flex-1 gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                    <section class="space-y-6">
                        <div class="inline-flex items-center gap-2 rounded-full border border-brand-100 bg-white/80 px-4 py-2 text-xs font-semibold text-brand-700 shadow-sm backdrop-blur">
                            Shared-hosting friendly stack
                        </div>

                        <div class="space-y-4">
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-slate-400">Field-ready foundation</p>
                            <h2 class="max-w-2xl text-4xl font-semibold leading-tight text-ink-950 sm:text-5xl">
                                Satu sistem untuk kunjungan outlet, outlet baru, dan monitoring performa cabang.
                            </h2>
                            <p class="max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">
                                Fondasi awal aplikasi SFA sudah siap: dokumentasi hidup, scaffold Laravel, stack mobile-first, dan arah UI modern yang aman dipakai tim sales di lapangan.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-3xl border border-white/70 bg-white/90 p-4 shadow-sm shadow-slate-200/60">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Roles</p>
                                <p class="mt-3 text-2xl font-semibold text-ink-950">4</p>
                                <p class="mt-2 text-sm text-slate-500">Admin pusat, supervisor, sales, dan SMD.</p>
                            </div>
                            <div class="rounded-3xl border border-white/70 bg-white/90 p-4 shadow-sm shadow-slate-200/60">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Docs</p>
                                <p class="mt-3 text-2xl font-semibold text-ink-950">12</p>
                                <p class="mt-2 text-sm text-slate-500">Dokumen living spec siap dipakai lintas AI dan developer.</p>
                            </div>
                            <div class="rounded-3xl border border-white/70 bg-white/90 p-4 shadow-sm shadow-slate-200/60">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">UI</p>
                                <p class="mt-3 text-2xl font-semibold text-ink-950">Mobile</p>
                                <p class="mt-2 text-sm text-slate-500">Desktop tabel, mobile card, interaktif tanpa full SPA.</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a
                                href="#docs"
                                class="inline-flex items-center justify-center rounded-2xl bg-ink-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:-translate-y-0.5 hover:bg-slate-800"
                            >
                                Lihat Dokumentasi
                            </a>
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                            >
                                Masuk ke Aplikasi
                            </a>
                            <div class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-600 shadow-sm">
                                Next: auth, RBAC, outlet, visit workflow
                            </div>
                        </div>
                    </section>

                    <section class="relative">
                        <div class="absolute -inset-4 rounded-[2rem] bg-brand-200/30 blur-3xl"></div>
                        <div class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-white/90 p-4 shadow-[0_30px_80px_-40px_rgba(15,23,42,0.55)] backdrop-blur sm:p-5">
                            <div class="rounded-[1.6rem] border border-slate-100 bg-slate-900 p-4 text-white shadow-inner shadow-slate-950/20">
                                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Supervisor Dashboard</p>
                                        <h3 class="mt-2 text-lg font-semibold">Cabang Bandung</h3>
                                    </div>
                                    <div class="rounded-2xl bg-white/10 px-3 py-2 text-right">
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Today</p>
                                        <p class="text-sm font-semibold">128 Visit</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-3xl bg-white/5 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pending Verifikasi</p>
                                        <p class="mt-3 text-3xl font-semibold text-white">18</p>
                                        <p class="mt-2 text-sm text-slate-400">NOO dan outlet baru menunggu review.</p>
                                    </div>
                                    <div class="rounded-3xl bg-brand-500/16 p-4 ring-1 ring-brand-400/30">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-200">Prospek Follow Up</p>
                                        <p class="mt-3 text-3xl font-semibold text-white">42</p>
                                        <p class="mt-2 text-sm text-brand-100/80">Siap dipantau sales dan supervisor.</p>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-3xl bg-white p-4 text-slate-900">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Search outlet</p>
                                            <h4 class="mt-1 text-sm font-semibold">Autocomplete tanpa reload</h4>
                                        </div>
                                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">Alpine.js</span>
                                    </div>

                                    <div class="mt-4 rounded-[1.4rem] border border-slate-200 bg-slate-50 p-3" x-data="{ query: '', outlets: ['Toko Maju Jaya', 'Salon Mawar', 'Barbershop Central'], open: false }">
                                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                <path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                                <circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" />
                                            </svg>
                                            <input
                                                x-model="query"
                                                @focus="open = true"
                                                @input="open = query.length > 0"
                                                type="text"
                                                placeholder="Ketik kode/nama outlet..."
                                                class="w-full border-0 bg-transparent text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none"
                                            >
                                        </div>

                                        <div x-show="open" x-transition class="mt-3 space-y-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-lg shadow-slate-200/60">
                                            <template x-for="outlet in outlets.filter(name => name.toLowerCase().includes(query.toLowerCase()))" :key="outlet">
                                                <button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm text-slate-600 transition hover:bg-slate-50">
                                                    <span x-text="outlet"></span>
                                                    <span class="text-xs font-semibold text-brand-600">Pilih</span>
                                                </button>
                                            </template>
                                            <p x-show="outlets.filter(name => name.toLowerCase().includes(query.toLowerCase())).length === 0" class="px-3 py-2 text-sm text-slate-400">
                                                Outlet belum ditemukan, lanjut buat outlet baru.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>

                <section id="docs" class="mt-8 rounded-[2rem] border border-white/80 bg-white/85 p-5 shadow-sm shadow-slate-200/70 backdrop-blur sm:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div class="max-w-2xl">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Living docs</p>
                            <h3 class="mt-2 text-2xl font-semibold text-ink-950">Dokumentasi kerja selalu di-update</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">
                                Gunakan `README.md` sebagai index utama, lalu lanjut ke `docs/00-getting-started.md` dan dokumen inti lain saat melanjutkan implementasi atau handoff ke AI lain.
                            </p>
                        </div>
                        <div class="rounded-2xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700">
                            Source of truth project
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-ink-950">01 Requirements</p>
                            <p class="mt-2 text-sm text-slate-500">Business rules, visit flow, outlet rules, and acceptance criteria.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-ink-950">02 Architecture</p>
                            <p class="mt-2 text-sm text-slate-500">Laravel, Blade, Alpine, Tailwind, Chart.js, and hosting direction.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-ink-950">03 Database Schema</p>
                            <p class="mt-2 text-sm text-slate-500">Core tables, outlet lifecycle, visits, and audit recommendations.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-ink-950">11 Decisions Log</p>
                            <p class="mt-2 text-sm text-slate-500">Tracks stack choices, UI rules, and implementation progress.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
