<x-app-layout>
    @php($user = auth()->user())
    @php($branchName = $user->branch?->name ?? 'Semua Cabang')
    <x-slot name="header">
        {{-- Greeting Hero --}}
        <div class="app-hero-gradient -mx-4 -mt-4 rounded-b-2xl px-5 py-6 sm:-mx-6 sm:px-8 sm:py-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium text-white/80">{{ now()->translatedFormat('l, d F Y') }} · {{ $branchName }}</p>
                    <h2 class="mt-1 text-2xl font-bold text-white sm:text-3xl">
                        Halo, {{ $user->name }}!
                    </h2>
                    <p class="mt-1 text-sm text-white/70">{{ $user->roleLabel() }} — siap produktif hari ini.</p>
                </div>
                @if ($user->isSales())
                    <a href="{{ route('sales-visits.create') }}" class="inline-flex items-center gap-3 rounded-xl bg-white px-5 py-3.5 shadow-lg shadow-sky-500/20 transition hover:shadow-xl hover:shadow-sky-500/30 active:scale-[0.98]">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-500 text-lg font-black text-white">+</span>
                        <span>
                            <span class="block text-sm font-bold text-slate-900">Input Kunjungan Sales</span>
                            <span class="text-xs text-slate-500">Tap untuk buat kunjungan baru</span>
                        </span>
                        <svg class="ml-1 h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @elseif ($user->isSmd())
                    <a href="{{ route('smd-visits.create') }}" class="inline-flex items-center gap-3 rounded-xl bg-white px-5 py-3.5 shadow-lg shadow-sky-500/20 transition hover:shadow-xl hover:shadow-sky-500/30 active:scale-[0.98]">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-500 text-lg font-black text-white">+</span>
                        <span>
                            <span class="block text-sm font-bold text-slate-900">Input Kunjungan SMD</span>
                            <span class="text-xs text-slate-500">Tap untuk buat kunjungan baru</span>
                        </span>
                        <svg class="ml-1 h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <div class="inline-flex items-center gap-3 rounded-xl bg-white/15 px-5 py-4 backdrop-blur-sm">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-white text-lg font-black text-sky-600 shadow-sm">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <span>
                            <span class="block text-sm font-bold text-white">{{ '@'.$user->username }}</span>
                            <span class="text-xs text-white/70">Siap untuk monitoring hari ini</span>
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    @php($defaultDashboard = $dashboardData)

    <div class="py-6 sm:py-8">
        <div
            class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8"
            x-data="dashboardView({
                activeTab: '{{ $user->isSupervisor() ? 'branch' : 'default' }}',
                datasets: {
                    default: @js($defaultDashboard),
                    branch: @js($supervisorBranchData),
                    personal: @js($supervisorPersonalData),
                },
                branchName: @js($branchName),
            })"
            x-init="initChart()"
        >
            @if ($user->isSupervisor())
                <section class="app-animate-enter">
                    <div class="inline-flex rounded-lg bg-white p-1 shadow-sm border border-slate-200">
                        <button type="button" @click="switchTab('branch')" :class="activeTab === 'branch' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="rounded-md px-5 py-2.5 text-sm font-semibold transition">
                            <span class="hidden sm:inline">Dashboard</span> Cabang
                        </button>
                        <button type="button" @click="switchTab('personal')" :class="activeTab === 'personal' ? 'bg-sky-500 text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="rounded-md px-5 py-2.5 text-sm font-semibold transition">
                            Aktivitas Saya
                        </button>
                    </div>
                </section>
            @endif

            <section class="app-animate-enter">
                <p class="mb-3 text-xs font-bold uppercase tracking-[0.15em] text-slate-400">Ringkasan Hari Ini</p>
                {{-- 3 compact KPI cards: mobile 2-col, desktop 3-col --}}
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-3">
                    <template x-for="metric in pulseMetrics" :key="metric.label">
                        <div class="relative rounded-xl border overflow-hidden bg-white shadow-sm transition hover:shadow-md"
                             :class="metricCardClass(metric.label)">
                            <div class="absolute left-0 top-0 h-full w-1 rounded-l-xl" :class="metricAccentClass(metric.label)"></div>
                            <div class="px-4 py-3.5 pl-5">
                                <p class="text-[11px] font-bold uppercase tracking-[0.16em]" :class="metricLabelClass(metric.label)" x-text="metric.label"></p>
                                <p class="mt-2 text-xl font-bold leading-tight" :class="metricValueClass(metric.label)" x-text="metric.value"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <section class="grid items-start gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <div class="app-panel app-animate-enter overflow-hidden p-5 sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-blue-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                                Trend Kunjungan
                            </p>
                            <h3 class="mt-2 text-xl font-bold text-slate-900">Kunjungan & Collection</h3>
                        </div>
                        <p class="text-sm text-slate-500" x-text="current.chartHelper"></p>
                    </div>
                    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 sm:p-5">
                        <div class="mb-4 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700"><span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>Kunjungan</span>
                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Collection</span>
                        </div>
                        <div class="h-[16rem] sm:h-[18rem]">
                            <canvas id="dashboard-performance-chart"></canvas>
                        </div>
                    </div>
                </div>

                @if (! $user->isSales() && ! $user->isSmd())
                    <div class="space-y-6">
                        <div class="app-panel app-animate-enter overflow-hidden p-5 sm:p-6">
                            <div class="absolute inset-x-0 top-0 h-1 bg-blue-500"></div>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-blue-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Customer Priority
                                    </p>
                                    <h3 class="mt-2 text-xl font-bold text-slate-900">Top Customer</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700">Order aktif</span>
                            </div>
                            <div class="mt-5 space-y-3">
                                <template x-if="current.topCustomers.length === 0">
                                    <div class="app-empty-state px-4 py-5">Belum ada customer dengan order tercatat.</div>
                                </template>
                                <template x-for="(customer, index) in current.topCustomers" :key="customer.id ?? customer.name ?? index">
                                     <div class="rounded-xl border border-blue-100 bg-gradient-to-r from-blue-50 to-white px-4 py-3.5 transition duration-200 hover:shadow-md">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-bold text-white shadow-sm">
                                                    <span x-text="index + 1"></span>
                                                </span>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900" x-text="customer.name"></p>
                                                    <p class="mt-1 text-xs leading-5 text-slate-500"><span x-text="customer.total_orders"></span> order · terakhir <span x-text="formatSimpleDate(customer.last_order_at)"></span></p>
                                                </div>
                                            </div>
                                            <span class="inline-flex min-w-[5rem] items-center justify-center rounded-full bg-blue-600 px-3 py-1.5 text-sm font-bold text-white shadow-sm" x-text="formatCurrency(customer.total_amount)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="app-panel app-animate-enter overflow-hidden p-5 sm:p-6">
                            <div class="absolute inset-x-0 top-0 h-1 bg-amber-500"></div>
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-amber-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                        Follow Up Supervisor
                                    </p>
                                    <h3 class="mt-2 text-xl font-bold text-slate-900">Lama Tidak Order</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1.5 text-xs font-bold text-amber-700">> 30 hari</span>
                            </div>
                            <div class="mt-5 space-y-3">
                                <template x-if="current.staleCustomers.length === 0">
                                    <div class="app-empty-state px-4 py-5">Belum ada customer yang melewati batas 30 hari tanpa order.</div>
                                </template>
                                <template x-for="customer in current.staleCustomers" :key="customer.id ?? customer.name">
                                    <div class="rounded-xl border border-amber-100 bg-gradient-to-r from-amber-50 to-white px-4 py-3.5 transition duration-200 hover:shadow-md">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 shadow-sm">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </span>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-900" x-text="customer.name"></p>
                                                    <p class="mt-1 text-xs text-slate-500">Terakhir order <span x-text="formatSimpleDate(customer.last_order_at)"></span> · <span x-text="customer.total_orders"></span> order total</p>
                                                </div>
                                            </div>
                                            <p class="inline-flex min-w-[5rem] items-center justify-center rounded-full bg-amber-500 px-3 py-1.5 text-sm font-bold text-white shadow-sm" x-text="daysSince(customer.last_order_at)"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                @endif
            </section>

            <section class="app-panel app-animate-enter overflow-hidden p-5 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Monitoring Operasional
                        </p>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">Daftar Kunjungan</h3>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1.5 text-[11px] font-bold tracking-wide text-white" x-text="`${current.recentVisits.length} visit`"></span>
                        <a href="{{ route('visit-history.index') }}" class="app-btn-sm-primary px-4 py-2.5">Buka History</a>
                    </div>
                </div>

                <div class="mt-5 hidden overflow-hidden rounded-lg border border-slate-200 lg:block">
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
                            <template x-for="visit in current.recentVisits" :key="visit.id">
                                <tr class="transition duration-200 hover:bg-sky-50/60">
                                    <td class="px-4 py-4 text-slate-600" x-text="formatVisitDate(visit.visited_at, visit.branch?.timezone)"></td>
                                    <td class="px-4 py-4 text-slate-900" x-text="visit.user?.name || '-' "></td>
                                    <td class="px-4 py-4 text-slate-600" x-text="visit.outlet?.name || '-' "></td>
                                    <td class="px-4 py-4"><span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700" x-text="String(visit.visit_type).toUpperCase()"></span></td>
                                     <td class="px-4 py-4"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" :class="visit.outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : (visit.outlet_condition === 'order_by_wa' ? 'bg-violet-50 text-violet-700' : 'bg-amber-50 text-amber-700')" x-text="formatOutletCondition(visit.outlet_condition)"></span></td>
                                    <td class="px-4 py-4 text-slate-900" x-text="formatCurrency(visitSalesAmount(visit))"></td>
                                    <td class="px-4 py-4 text-slate-900" x-text="formatCurrency(visitCollection(visit))"></td>
                                    <td class="px-4 py-4"><a :href="`/visit-history/${visit.id}`" class="inline-flex items-center rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-900 shadow-sm shadow-sky-100/50 transition hover:bg-sky-100">Detail</a></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 space-y-3 lg:hidden">
                    <template x-if="current.recentVisits.length === 0">
                        <div class="app-empty-state px-4 py-5">Belum ada aktivitas yang tercatat di scope ini.</div>
                    </template>
                    <template x-for="visit in current.recentVisits" :key="visit.id">
                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm transition hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-slate-900" x-text="visit.outlet?.name || '-' "></p>
                                    <p class="mt-1 text-xs text-slate-500" x-text="`${String(visit.visit_type).toUpperCase()} · ${visit.user?.name || '-'}`"></p>
                                </div>
                                <p class="text-xs text-slate-400" x-text="formatVisitDate(visit.visited_at, visit.branch?.timezone)"></p>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-3">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-blue-600">Sales Amount</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900" x-text="formatCurrency(visitSalesAmount(visit))"></p>
                                </div>
                                <div class="rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-3">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-emerald-600">Collection</p>
                                    <p class="mt-2 text-sm font-bold text-slate-900" x-text="formatCurrency(visitCollection(visit))"></p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-bold" :class="visit.outlet_condition === 'buka' ? 'bg-emerald-100 text-emerald-700' : (visit.outlet_condition === 'order_by_wa' ? 'bg-violet-100 text-violet-700' : 'bg-amber-100 text-amber-700')" x-text="formatOutletCondition(visit.outlet_condition)"></span>
                                <a :href="`/visit-history/${visit.id}`" class="app-btn-sm-primary px-4 py-2">Detail</a>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            function dashboardView(config) {
                return {
                    activeTab: config.activeTab,
                    datasets: config.datasets,
                    branchName: config.branchName,
                    chart: null,
                    get current() {
                        return this.datasets[this.activeTab] || this.datasets.default;
                    },
                    // Selalu 3 KPI: index 0 (Visit), 1 (Sales Amount), 2 (Collection)
                    get pulseMetrics() {
                        const metrics = this.current?.metrics || [];
                        return metrics.slice(0, 3).filter(Boolean);
                    },
                    metricTone(label) {
                        const value = String(label || '').toLowerCase();

                        if (value.includes('collection')) {
                            return 'emerald';
                        }

                        if (value.includes('pending') || value.includes('inactive')) {
                            return 'amber';
                        }

                        if (value.includes('visit')) {
                            return 'blue';
                        }

                        if (value.includes('sales') || value.includes('amount') || value.includes('po')) {
                            return 'sky';
                        }

                        return 'slate';
                    },
                    metricCardClass(label) {
                        return {
                            blue: 'border-blue-200 bg-gradient-to-br from-blue-50 to-white',
                            emerald: 'border-emerald-200 bg-gradient-to-br from-emerald-50 to-white',
                            amber: 'border-amber-200 bg-gradient-to-br from-amber-50 to-white',
                            sky: 'border-sky-200 bg-gradient-to-br from-sky-50 to-white',
                            slate: 'border-slate-200 bg-white',
                        }[this.metricTone(label)];
                    },
                    metricAccentClass(label) {
                        return {
                            blue: 'bg-blue-500',
                            emerald: 'bg-emerald-500',
                            amber: 'bg-amber-500',
                            sky: 'bg-sky-500',
                            slate: 'bg-slate-400',
                        }[this.metricTone(label)];
                    },
                    metricLabelClass(label) {
                        return {
                            blue: 'text-blue-600',
                            emerald: 'text-emerald-600',
                            amber: 'text-amber-600',
                            sky: 'text-sky-600',
                            slate: 'text-slate-400',
                        }[this.metricTone(label)];
                    },
                    metricValueClass(label) {
                        return {
                            blue: 'text-blue-700',
                            emerald: 'text-emerald-700',
                            amber: 'text-amber-700',
                            sky: 'text-sky-700',
                            slate: 'text-slate-900',
                        }[this.metricTone(label)];
                    },
                    initChart() {
                        this.$nextTick(() => this.renderChart());
                    },
                    switchTab(tab) {
                        this.activeTab = tab;
                        this.$nextTick(() => this.renderChart());
                    },
                    renderChart() {
                        const chartElement = document.getElementById('dashboard-performance-chart');

                        if (!chartElement || typeof window.Chart === 'undefined') {
                            return;
                        }

                        if (this.chart) {
                            this.chart.destroy();
                        }

                        this.chart = new window.Chart(chartElement, {
                            type: 'line',
                            data: {
                                labels: this.current.chartLabels,
                                datasets: [
                                    {
                                        label: 'Kunjungan',
                                        data: this.current.chartValues,
                                        borderColor: '#2563eb',
                                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                                        tension: 0.38,
                                        fill: false,
                                        pointRadius: 3,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: '#2563eb',
                                        yAxisID: 'yVisits',
                                    },
                                    {
                                        label: 'Collection',
                                        data: this.current.collectionValues || [],
                                        borderColor: '#10b981',
                                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                                        tension: 0.38,
                                        fill: false,
                                        pointRadius: 3,
                                        pointHoverRadius: 6,
                                        pointBackgroundColor: '#10b981',
                                        yAxisID: 'yCollection',
                                    }
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                scales: {
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: '#64748b' },
                                    },
                                    yVisits: {
                                        type: 'linear',
                                        position: 'left',
                                        beginAtZero: true,
                                        grid: { color: 'rgba(148, 163, 184, 0.14)' },
                                        title: {
                                            display: true,
                                            text: 'Kunjungan',
                                            color: '#475569',
                                            font: { weight: '600' },
                                        },
                                        ticks: {
                                            color: '#64748b',
                                            precision: 0,
                                        },
                                    },
                                    yCollection: {
                                        type: 'linear',
                                        position: 'right',
                                        beginAtZero: true,
                                        grid: { display: false },
                                        title: {
                                            display: true,
                                            text: 'Collection',
                                            color: '#475569',
                                            font: { weight: '600' },
                                        },
                                        ticks: {
                                            color: '#64748b',
                                            callback: (value) => new Intl.NumberFormat('id-ID', {
                                                maximumFractionDigits: 0,
                                            }).format(value),
                                        },
                                    },
                                },
                                plugins: {
                                    legend: {
                                        display: false,
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                                        borderColor: 'rgba(148, 163, 184, 0.18)',
                                        borderWidth: 1,
                                        padding: 12,
                                        callbacks: {
                                            label: (context) => {
                                                if (context.dataset.label === 'Collection') {
                                                    return `${context.dataset.label}: Rp ${new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(context.raw || 0)}`;
                                                }

                                                return `${context.dataset.label}: ${context.raw}`;
                                            },
                                        },
                                    },
                                },
                            },
                        });
                    },
                    formatVisitDate(value, timezone = 'Asia/Jakarta') {
                        if (!value) {
                            return '-';
                        }

                        const date = new Date(value);

                        if (Number.isNaN(date.getTime())) {
                            return value;
                        }

                        return new Intl.DateTimeFormat('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            hour: '2-digit',
                            minute: '2-digit',
                            timeZone: timezone,
                        }).format(date);
                    },
                    formatSimpleDate(value, timezone = 'Asia/Jakarta') {
                        if (!value) {
                            return '-';
                        }

                        const date = new Date(value);

                        if (Number.isNaN(date.getTime())) {
                            return value;
                        }

                        return new Intl.DateTimeFormat('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            timeZone: timezone,
                        }).format(date);
                    },
                    formatOutletCondition(value) {
                        return {
                            buka: 'Buka',
                            tutup: 'Tutup',
                            order_by_wa: 'Order by WA',
                        }[value] || '-';
                    },
                    daysSince(value) {
                        if (!value) {
                            return '-';
                        }

                        const date = new Date(value);

                        if (Number.isNaN(date.getTime())) {
                            return '-';
                        }

                        const diff = Math.max(0, Math.floor((Date.now() - date.getTime()) / 86400000));

                        return `${diff} hari`;
                    },
                    visitSalesAmount(visit) {
                        return Number(visit?.visit_type === 'sales' ? (visit?.sales_detail?.order_amount || 0) : (visit?.smd_detail?.po_amount || 0));
                    },
                    visitCollection(visit) {
                        return Number(visit?.visit_type === 'sales' ? (visit?.sales_detail?.receivable_amount || 0) : (visit?.smd_detail?.payment_amount || 0));
                    },
                    formatCurrency(amount) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumFractionDigits: 0,
                        }).format(Number(amount || 0));
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
