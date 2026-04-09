<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="app-overline">Supervisor Workflow</p>
                <h2 class="app-page-title mt-2">Verifikasi Outlet</h2>
                <p class="app-body-copy mt-2 max-w-3xl">Daftar outlet pending yang menunggu official kode dari supervisor.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="app-alert app-alert-success">
                    <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/80 text-emerald-600 shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="m5 13 4 4L19 7" /></svg>
                    </span>
                    <div class="min-w-0">
                        <p class="text-[12px] font-semibold uppercase tracking-[0.14em] text-emerald-700">Sukses</p>
                        <p class="mt-1 font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <section class="app-panel p-5">
                <form method="GET" class="grid gap-3 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <x-input-label for="search" value="Cari outlet" />
                        <div class="app-input-shell mt-2">
                            <span class="app-input-icon">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                            </span>
                            <x-text-input id="search" name="search" class="app-field-with-icon block w-full" :value="$filters['search']" placeholder="Nama outlet, official kode, kecamatan, kota" />
                        </div>
                    </div>
                    <div class="md:col-span-3 flex flex-wrap gap-3">
                        <x-primary-button>Terapkan Filter</x-primary-button>
                        <a href="{{ route('outlet-verifications.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
                <div class="app-table-shell hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Official Kode</th>
                                <th class="px-4 py-3 font-semibold">Status Outlet</th>
                                <th class="px-4 py-3 font-semibold">Pembuat</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($outlets as $outlet)
                                <tr>
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->official_kode ?: '-' }}</td>
                                    <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->outlet_status === 'prospek' ? 'bg-violet-50 text-violet-700' : ($outlet->outlet_status === 'pending' ? 'bg-amber-50 text-amber-700' : ($outlet->outlet_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-700')) }}">{{ $outlet->statusLabel() }}</span></td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->creator?->name ?? '-' }}</td>
                                    <td class="px-4 py-4"><a href="{{ route('outlet-verifications.edit', $outlet) }}" class="app-action-secondary min-h-[2.8rem] px-4 py-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7" /></svg>Tindak Lanjut</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada outlet yang perlu ditinjau.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($outlets as $outlet)
                        <div class="app-soft-panel p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->outlet_status === 'prospek' ? 'bg-violet-50 text-violet-700' : ($outlet->outlet_status === 'pending' ? 'bg-amber-50 text-amber-700' : ($outlet->outlet_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600')) }}">{{ $outlet->statusLabel() }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Status outlet</p>
                                    <p class="mt-1">{{ $outlet->statusLabel() }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Official Kode</p>
                                    <p class="mt-1">{{ $outlet->official_kode ?: '-' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('outlet-verifications.edit', $outlet) }}" class="app-action-secondary mt-4 inline-flex min-h-[2.8rem] px-4 py-2"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5 7 7-7 7" /></svg>Tindak Lanjut</a>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada outlet yang perlu ditinjau.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $outlets->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
