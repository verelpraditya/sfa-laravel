<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Daftar Operasional</p>
            <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">{{ $title }}</h2>
            <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">{{ $description }}</p>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="app-panel p-5">
                <form method="GET" class="grid gap-3 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Cari outlet" />
                        <x-text-input id="search" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Nama, official kode, kecamatan, kota" />
                    </div>
                    <div class="md:col-span-1 flex items-end gap-3">
                        <x-primary-button>Terapkan</x-primary-button>
                        <a href="{{ url()->current() }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block shadow-[0_18px_40px_-30px_rgba(15,23,42,0.28)]">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Cabang</th>
                                <th class="px-4 py-3 font-semibold">Jenis</th>
                                <th class="px-4 py-3 font-semibold">Official Kode</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
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
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->branch?->name }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->typeLabel() }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->official_kode ?: '-' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->outlet_status === 'inactive' ? 'bg-slate-100 text-slate-600' : 'bg-sky-50 text-sky-700' }}">{{ $outlet->statusLabel() }}</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if (auth()->user()->canVerifyOutlets())
                                            <a href="{{ route('outlet-verifications.edit', $outlet) }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">
                                                {{ $variant === 'prospek' ? 'Lihat Outlet' : 'Tindak Lanjut' }}
                                            </a>
                                        @else
                                            <span class="text-xs font-semibold text-slate-400">View only</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data untuk daftar ini.</td>
                                </tr>
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
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->outlet_status === 'inactive' ? 'bg-slate-100 text-slate-600' : 'bg-sky-50 text-sky-700' }}">{{ $outlet->statusLabel() }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Cabang</p>
                                    <p class="mt-1">{{ $outlet->branch?->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Jenis</p>
                                    <p class="mt-1">{{ $outlet->typeLabel() }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Official Kode</p>
                                    <p class="mt-1">{{ $outlet->official_kode ?: '-' }}</p>
                                </div>
                            </div>
                            @if (auth()->user()->canVerifyOutlets())
                                <a href="{{ route('outlet-verifications.edit', $outlet) }}" class="mt-4 inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80">
                                    {{ $variant === 'prospek' ? 'Lihat Outlet' : 'Tindak Lanjut' }}
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada data untuk daftar ini.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $outlets->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
