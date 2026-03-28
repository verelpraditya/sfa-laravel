<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Kunjungan SMD</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Riwayat Aktivitas SMD</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">Pantau aktivitas PO, display, tukar faktur, dan tagihan yang sudah dikerjakan tim SMD.</p>
            </div>
            <a href="{{ route('smd-visits.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] px-5 py-3 text-sm font-semibold text-white shadow-[0_20px_40px_-18px_rgba(29,78,216,0.75)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_46px_-18px_rgba(29,78,216,0.9)]">
                Input Kunjungan SMD
            </a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('status') }}</div>
            @endif

            <section class="app-panel p-5">
                <form method="GET" class="grid gap-3 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Cari outlet / official kode" />
                        <x-text-input id="search" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Mis. Toko Sinar atau OFF-BDG-001" />
                    </div>
                    <div>
                        <x-input-label for="activity" value="Aktivitas" />
                        <select id="activity" name="activity" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">Semua</option>
                            <option value="ambil_po" @selected($filters['activity'] === 'ambil_po')>Ambil PO</option>
                            <option value="merapikan_display" @selected($filters['activity'] === 'merapikan_display')>Merapikan Display</option>
                            <option value="tukar_faktur" @selected($filters['activity'] === 'tukar_faktur')>Tukar Faktur</option>
                            <option value="ambil_tagihan" @selected($filters['activity'] === 'ambil_tagihan')>Ambil Tagihan</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap gap-3">
                        <x-primary-button>Terapkan Filter</x-primary-button>
                        <a href="{{ route('smd-visits.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block shadow-[0_18px_40px_-30px_rgba(15,23,42,0.28)]">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Aktivitas</th>
                                <th class="px-4 py-3 font-semibold">Nominal PO</th>
                                <th class="px-4 py-3 font-semibold">Nominal Pembayaran</th>
                                <th class="px-4 py-3 font-semibold">Waktu</th>
                                <th class="px-4 py-3 font-semibold">Pelaksana</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($visits as $visit)
                                <tr>
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $visit->outlet?->district }}, {{ $visit->outlet?->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->smdActivities->pluck('activity_type')->map(fn ($item) => str($item)->replace('_', ' ')->title())->implode(', ') }}</td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->smdDetail?->po_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) $visit->smdDetail?->payment_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $visit->user?->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada kunjungan SMD yang tersimpan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($visits as $visit)
                        <div class="app-soft-panel p-4">
                            <p class="font-semibold text-slate-900">{{ $visit->outlet?->name }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</p>
                            <p class="mt-3 text-sm text-slate-600">{{ $visit->smdActivities->pluck('activity_type')->map(fn ($item) => str($item)->replace('_', ' ')->title())->implode(', ') }}</p>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada kunjungan SMD yang tersimpan.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
