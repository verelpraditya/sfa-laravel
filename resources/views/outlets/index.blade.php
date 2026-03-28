<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Master Outlet</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Outlet Cabang</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">Kelola outlet yang sudah terdaftar, cek status verifikasi, dan siapkan data untuk autocomplete kunjungan tanpa reload.</p>
            </div>
            <a href="{{ route('outlets.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-ink-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800">
                Tambah Outlet
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                    <form method="GET" action="{{ route('outlets.index') }}" class="grid gap-3 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <x-input-label for="search" value="Cari outlet" />
                            <x-text-input id="search" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Nama outlet, official kode, kecamatan, kota" />
                        </div>
                        <div>
                            <x-input-label for="status" value="Verifikasi" />
                            <select id="status" name="status" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                                <option value="">Semua</option>
                                <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                                <option value="verified" @selected($filters['status'] === 'verified')>Verified</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="type" value="Jenis outlet" />
                            <select id="type" name="type" class="mt-2 block w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm shadow-slate-200/60 focus:border-brand-400 focus:ring-4 focus:ring-brand-100">
                                <option value="">Semua</option>
                                <option value="prospek" @selected($filters['type'] === 'prospek')>Prospek</option>
                                <option value="noo" @selected($filters['type'] === 'noo')>NOO</option>
                                <option value="pelanggan_lama" @selected($filters['type'] === 'pelanggan_lama')>Pelanggan Lama</option>
                            </select>
                        </div>
                        <div class="md:col-span-4 flex flex-wrap gap-3">
                            <x-primary-button>Terapkan Filter</x-primary-button>
                            <a href="{{ route('outlets.index') }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-200/60 transition hover:border-slate-300 hover:bg-slate-50">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60" x-data="outletSearchPreview()">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Autocomplete Preview</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Cari outlet tanpa reload</h3>
                        </div>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">AJAX</span>
                    </div>

                    <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-3">
                        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
                            <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M14.167 14.166 17.5 17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <circle cx="8.75" cy="8.75" r="5.833" stroke="currentColor" stroke-width="1.8" />
                            </svg>
                            <input x-model="query" @input.debounce.300ms="search" type="text" placeholder="Ketik kode/nama outlet..." class="w-full border-0 bg-transparent text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none">
                        </div>

                        <div class="mt-3 space-y-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm" x-show="query.length > 0">
                            <template x-for="item in results" :key="item.id">
                                <div class="rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="font-semibold text-slate-800" x-text="item.name"></p>
                                        <span class="text-xs font-semibold text-brand-700" x-text="item.official_kode || 'Belum ada kode'"></span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500" x-text="`${item.district}, ${item.city}`"></p>
                                </div>
                            </template>
                            <p x-show="!loading && results.length === 0" class="px-3 py-2 text-sm text-slate-400">Belum ada outlet yang cocok.</p>
                            <p x-show="loading" class="px-3 py-2 text-sm text-slate-400">Mencari outlet...</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Cabang</th>
                                <th class="px-4 py-3 font-semibold">Jenis</th>
                                <th class="px-4 py-3 font-semibold">Official Kode</th>
                                <th class="px-4 py-3 font-semibold">Verifikasi</th>
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
                                    <td class="px-4 py-4 text-slate-600">{{ str($outlet->outlet_type)->replace('_', ' ')->title() }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $outlet->official_kode ?: '-' }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->verification_status === 'verified' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ ucfirst($outlet->verification_status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <a href="{{ route('outlets.edit', $outlet) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada outlet sesuai filter saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($outlets as $outlet)
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $outlet->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $outlet->district }}, {{ $outlet->city }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $outlet->verification_status === 'verified' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ ucfirst($outlet->verification_status) }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Cabang</p>
                                    <p class="mt-1">{{ $outlet->branch?->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Jenis</p>
                                    <p class="mt-1">{{ str($outlet->outlet_type)->replace('_', ' ')->title() }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Official Kode</p>
                                    <p class="mt-1">{{ $outlet->official_kode ?: '-' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('outlets.edit', $outlet) }}" class="mt-4 inline-flex items-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-200/60">Edit Outlet</a>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada outlet sesuai filter saat ini.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $outlets->links() }}</div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            function outletSearchPreview() {
                return {
                    query: '',
                    loading: false,
                    results: [],
                    async search() {
                        if (this.query.trim().length === 0) {
                            this.results = [];
                            return;
                        }

                        this.loading = true;

                        try {
                            const response = await fetch(`{{ route('ajax.outlets.search') }}?q=${encodeURIComponent(this.query)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                            });

                            const payload = await response.json();
                            this.results = payload.data || [];
                        } catch (error) {
                            this.results = [];
                        } finally {
                            this.loading = false;
                        }
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
