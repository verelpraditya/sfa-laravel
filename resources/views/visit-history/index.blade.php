<x-app-layout>
    <x-slot name="header">
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <p class="text-sm font-medium text-white/70">Monitoring Operasional</p>
            <h2 class="mt-1 text-2xl font-bold text-white sm:text-3xl">History Kunjungan</h2>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- KPI Section — computed from DB, role-appropriate --}}
            <section class="app-animate-enter">
                @php
                    $kpiCount = count($kpi);
                    $kpiGridClass = match (true) {
                        $kpiCount >= 5 => 'grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5',
                        $kpiCount === 4 => 'grid-cols-2 gap-3 sm:grid-cols-4',
                        default         => 'grid-cols-2 gap-3 sm:grid-cols-3',
                    };

                    $kpiColorMap = [
                        'blue'    => ['card' => 'app-kpi-blue',    'text' => 'text-blue-600'],
                        'sky'     => ['card' => 'app-kpi-sky',     'text' => 'text-sky-600'],
                        'violet'  => ['card' => 'app-kpi-violet',  'text' => 'text-violet-600'],
                        'emerald' => ['card' => 'app-kpi-emerald', 'text' => 'text-emerald-600'],
                        'amber'   => ['card' => 'app-kpi-amber',   'text' => 'text-amber-600'],
                    ];
                @endphp
                <div class="grid {{ $kpiGridClass }}">
                    @foreach ($kpi as $item)
                        @php($colors = $kpiColorMap[$item['color']] ?? $kpiColorMap['blue'])
                        <div class="{{ $colors['card'] }}">
                            <p class="pl-2 text-[11px] font-bold uppercase tracking-[0.16em] {{ $colors['text'] }}">{{ $item['label'] }}</p>
                            <p class="mt-2 pl-2 text-lg font-bold text-slate-900 sm:mt-3 sm:text-xl">{{ $item['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Filter Section --}}
            <section class="app-panel app-animate-enter rounded-xl p-4"
                     x-data="{ filtersOpen: false, search: '{{ str_replace("'", "\\'", $filters['search'] ?? '') }}' }">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="app-overline">Filter</p>
                        <h3 class="app-section-title mt-2">Temukan kunjungan yang kamu butuhkan</h3>
                    </div>
                </div>

                <form method="GET" action="{{ route('visit-history.index') }}" class="mt-5">
                    {{-- Hidden search field synced via Alpine --}}
                    <input type="hidden" name="search" :value="search">

                    {{-- Mobile: always-visible search + toggle --}}
                    <div class="flex items-end gap-2 lg:hidden">
                        <div class="flex-1">
                            <x-input-label for="search_mobile" value="Cari user / outlet" />
                            <div class="app-input-shell mt-2">
                                <span class="app-input-icon">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                                </span>
                                <input id="search_mobile" type="text" x-model="search" class="app-field-with-icon block w-full" placeholder="Nama user atau outlet" />
                            </div>
                        </div>
                        <button type="button" class="app-action-secondary min-h-[3rem] px-4" @click="filtersOpen = !filtersOpen">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 .8 1.6L14 13.28V19a1 1 0 0 1-.55.9l-4 2A1 1 0 0 1 8 21v-7.72L1.2 4.6A1 1 0 0 1 2 3h1" /></svg>
                            <span x-text="filtersOpen ? 'Tutup' : 'Filter'">Filter</span>
                        </button>
                    </div>

                    {{-- Collapsible on mobile, always visible on desktop --}}
                    <div class="mt-3 hidden lg:mt-0 lg:!block"
                         :class="{ '!block': filtersOpen }">
                        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                            <div>
                                <x-input-label for="from" value="Dari tanggal" />
                                <x-text-input id="from" name="from" type="date" class="mt-2 block w-full" :value="$filters['from']" />
                            </div>
                            <div>
                                <x-input-label for="to" value="Sampai tanggal" />
                                <x-text-input id="to" name="to" type="date" class="mt-2 block w-full" :value="$filters['to']" />
                            </div>
                            <div>
                                <x-input-label for="type" value="Tipe" />
                                <select id="type" name="type" class="app-select mt-2 block w-full">
                                    <option value="">Semua</option>
                                    <option value="sales" @selected($filters['type'] === 'sales')>Sales</option>
                                    <option value="smd" @selected($filters['type'] === 'smd')>SMD</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="condition" value="Kondisi" />
                                <select id="condition" name="condition" class="app-select mt-2 block w-full">
                                    <option value="">Semua</option>
                                    <option value="buka" @selected($filters['condition'] === 'buka')>Buka</option>
                                    <option value="tutup" @selected($filters['condition'] === 'tutup')>Tutup</option>
                                    <option value="order_by_wa" @selected($filters['condition'] === 'order_by_wa')>Order by WA</option>
                                </select>
                            </div>
                            {{-- Desktop search (hidden on mobile since mobile search is above) --}}
                            <div class="hidden lg:block xl:col-span-2">
                                <x-input-label for="search_desktop" value="Cari user / outlet" />
                                <div class="app-input-shell mt-2">
                                    <span class="app-input-icon">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M14.167 14.166 17.5 17.5" stroke-width="1.8" stroke-linecap="round" /><circle cx="8.75" cy="8.75" r="5.833" stroke-width="1.8" /></svg>
                                    </span>
                                    <input id="search_desktop" type="text" x-model="search" class="app-field-with-icon block w-full" placeholder="Nama user atau outlet" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                        <button type="submit" class="app-action-primary justify-center sm:min-w-[180px]">Terapkan</button>
                        <a href="{{ route('visit-history.index') }}" class="app-action-secondary justify-center sm:min-w-[140px]">Reset</a>
                    </div>
                </form>
            </section>

            {{-- Data Section --}}
            <section class="app-panel app-animate-enter rounded-xl p-4 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Daftar History</p>
                        <h3 class="app-section-title mt-2">Urutan kunjungan terbaru</h3>
                    </div>
                    <span class="app-chip">{{ $visits->total() }} data</span>
                </div>

                {{-- Desktop Table --}}
                <div class="app-table-shell mt-5 hidden lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3.5 font-semibold">Waktu</th>
                                <th class="px-4 py-3.5 font-semibold">User</th>
                                <th class="px-4 py-3.5 font-semibold">Outlet</th>
                                <th class="px-4 py-3.5 font-semibold">Tipe</th>
                                <th class="px-4 py-3.5 font-semibold">Kondisi</th>
                                <th class="px-4 py-3.5 font-semibold">Sales Amount</th>
                                <th class="px-4 py-3.5 font-semibold">Collection</th>
                                <th class="px-4 py-3.5 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($visits as $visit)
                                <tr class="transition duration-150 hover:bg-slate-50">
                                    <td class="px-4 py-3.5 text-slate-600">{{ $visit->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3.5 text-slate-900">{{ $visit->user?->name }}</td>
                                    <td class="px-4 py-3.5 text-slate-600">{{ $visit->outlet?->name }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="app-badge {{ $visit->visit_type === 'sales' ? 'app-badge-sky' : 'app-badge-violet' }}">
                                            {{ $visit->typeLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <span class="app-badge {{ $visit->outlet_condition === 'buka' ? 'app-badge-emerald' : ($visit->outlet_condition === 'tutup' ? 'app-badge-rose' : ($visit->outlet_condition === 'order_by_wa' ? 'app-badge-violet' : 'app-badge-slate')) }}">
                                            {{ $visit->outlet_condition === 'order_by_wa' ? 'Order by WA' : ($visit->outlet_condition ?: '-') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-900">Rp {{ number_format($visit->salesAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5 text-slate-900">Rp {{ number_format($visit->collectionAmount(), 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('visit-history.show', $visit) }}" class="app-btn-sm">Detail</a>
                                            @if ($canEdit)
                                                <a href="{{ route('visit-history.edit', $visit) }}" class="app-btn-sm">Edit</a>
                                                <form method="POST" action="{{ route('visit-history.destroy', $visit) }}" onsubmit="return confirm('Yakin hapus kunjungan ini? Data dan foto akan dihapus permanen.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="app-btn-sm text-rose-600 hover:bg-rose-50">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-0">
                                        <div class="app-empty-state m-4">Belum ada history kunjungan.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Cards — Alpine.js Infinite Scroll --}}
                <div class="mt-5 lg:hidden"
                     x-data="visitInfiniteScroll({
                         initial: @js($mobileInitialData),
                         baseUrl: '{{ route('visit-history.index') }}',
                         filters: @js($filters),
                     })"
                     x-init="boot()">

                    <div class="space-y-3">
                        <template x-for="visit in items" :key="visit.id">
                            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md">
                                {{-- Header: user + outlet + time --}}
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-slate-900" x-text="visit.user_name"></p>
                                        <p class="mt-0.5 truncate text-xs text-slate-500" x-text="visit.outlet_name"></p>
                                    </div>
                                    <p class="shrink-0 text-xs text-slate-400" x-text="visit.visited_at_formatted"></p>
                                </div>

                                {{-- Badges inline --}}
                                <div class="mt-2.5 flex flex-wrap gap-1.5">
                                    <span class="app-badge" :class="visit.visit_type === 'sales' ? 'app-badge-sky' : 'app-badge-violet'" x-text="visit.visit_type.toUpperCase()"></span>
                                    <span class="app-badge" :class="conditionBadgeClass(visit.outlet_condition)" x-text="conditionLabel(visit.outlet_condition)"></span>
                                </div>

                                {{-- Amounts --}}
                                <div class="mt-3 grid grid-cols-2 gap-3">
                                    <div class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-2.5">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-600">Sales Amount</p>
                                        <p class="mt-0.5 text-sm font-bold text-slate-900" x-text="formatCurrency(visit.sales_amount)"></p>
                                    </div>
                                    <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-2.5">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-600">Collection</p>
                                        <p class="mt-0.5 text-sm font-bold text-slate-900" x-text="formatCurrency(visit.collection_amount)"></p>
                                    </div>
                                </div>

                                {{-- Action --}}
                                <div class="mt-3 flex items-center justify-end gap-2">
                                    <template x-if="visit.can_edit">
                                        <a :href="visit.url_edit" class="app-btn-sm">Edit</a>
                                    </template>
                                    <template x-if="visit.can_edit">
                                        <form method="POST" :action="visit.url_destroy" onsubmit="return confirm('Yakin hapus kunjungan ini? Data dan foto akan dihapus permanen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-btn-sm text-rose-600 hover:bg-rose-50">Hapus</button>
                                        </form>
                                    </template>
                                    <a :href="visit.url_show" class="app-btn-sm-primary">Lihat Detail</a>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Sentinel element for IntersectionObserver --}}
                    <div x-ref="sentinel" class="h-4"></div>

                    {{-- Loading indicator --}}
                    <div x-show="loading" x-cloak class="flex items-center justify-center gap-2 py-6">
                        <svg class="h-5 w-5 animate-spin text-sky-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-sm font-medium text-slate-500">Memuat data...</span>
                    </div>

                    {{-- All loaded indicator --}}
                    <div x-show="!hasMore && items.length > 0" x-cloak class="py-4 text-center text-xs font-medium text-slate-400">
                        Semua data telah dimuat
                    </div>

                    {{-- Empty state --}}
                    <div x-show="!loading && items.length === 0" x-cloak class="app-empty-state">
                        Belum ada history kunjungan.
                    </div>
                </div>

                {{-- Desktop pagination only --}}
                <div class="mt-5 hidden lg:block">{{ $visits->links() }}</div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            function visitInfiniteScroll({ initial, baseUrl, filters }) {
                return {
                    items: initial.data || [],
                    currentPage: initial.meta?.current_page || 1,
                    lastPage: initial.meta?.last_page || 1,
                    loading: false,
                    observer: null,

                    get hasMore() {
                        return this.currentPage < this.lastPage;
                    },

                    boot() {
                        if (!this.hasMore) return;

                        this.$nextTick(() => {
                            const sentinel = this.$refs.sentinel;
                            if (!sentinel) return;

                            this.observer = new IntersectionObserver(
                                ([entry]) => {
                                    if (entry.isIntersecting && !this.loading && this.hasMore) {
                                        this.loadMore();
                                    }
                                },
                                { rootMargin: '200px' }
                            );
                            this.observer.observe(sentinel);
                        });
                    },

                    async loadMore() {
                        this.loading = true;

                        try {
                            const params = new URLSearchParams();
                            if (filters.from) params.set('from', filters.from);
                            if (filters.to) params.set('to', filters.to);
                            if (filters.type) params.set('type', filters.type);
                            if (filters.search) params.set('search', filters.search);
                            if (filters.condition) params.set('condition', filters.condition);
                            params.set('page', this.currentPage + 1);

                            const response = await fetch(baseUrl + '?' + params.toString(), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            if (!response.ok) throw new Error('Network error');

                            const json = await response.json();
                            this.items = this.items.concat(json.data || []);
                            this.currentPage = json.meta?.current_page || this.currentPage;
                            this.lastPage = json.meta?.last_page || this.lastPage;

                            // Stop observing if no more pages
                            if (!this.hasMore && this.observer) {
                                this.observer.disconnect();
                            }
                        } catch (err) {
                            console.error('Infinite scroll error:', err);
                        } finally {
                            this.loading = false;
                        }
                    },

                    conditionBadgeClass(condition) {
                        return {
                            'buka': 'app-badge-emerald',
                            'tutup': 'app-badge-rose',
                            'order_by_wa': 'app-badge-violet',
                        }[condition] || 'app-badge-slate';
                    },

                    conditionLabel(condition) {
                        return {
                            'buka': 'Buka',
                            'tutup': 'Tutup',
                            'order_by_wa': 'Order by WA',
                        }[condition] || '-';
                    },

                    formatCurrency(amount) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0,
                        }).format(Number(amount || 0));
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
