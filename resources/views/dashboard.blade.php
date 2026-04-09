<x-app-layout>
    @php($user = auth()->user())
    @php($branchName = $user->branch?->name ?? 'Semua Cabang')
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="app-chip">{{ $user->roleLabel() }}</span>
                    <span class="app-chip">{{ $branchName }}</span>
                    <span class="app-chip">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <h2 class="app-page-title mt-4">Dashboard</h2>
            </div>
            @if ($user->isSales())
                <a href="{{ route('sales-visits.create') }}" class="app-panel app-card-interactive overflow-hidden px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[1.2rem] bg-[radial-gradient(circle_at_top,_#67e8f9,_#38bdf8_45%,_#2563eb_100%)] text-lg font-black text-white shadow-[0_16px_34px_-18px_rgba(56,189,248,0.85)]">+</div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Aksi Cepat</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">Input Kunjungan Sales</p>
                            <p class="text-xs text-slate-500">Tap untuk langsung buat kunjungan baru</p>
                        </div>
                    </div>
                </a>
            @elseif ($user->isSmd())
                <a href="{{ route('smd-visits.create') }}" class="app-panel app-card-interactive overflow-hidden px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[1.2rem] bg-[radial-gradient(circle_at_top,_#67e8f9,_#38bdf8_45%,_#2563eb_100%)] text-lg font-black text-white shadow-[0_16px_34px_-18px_rgba(56,189,248,0.85)]">+</div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Aksi Cepat</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">Input Kunjungan SMD</p>
                            <p class="text-xs text-slate-500">Tap untuk langsung buat kunjungan baru</p>
                        </div>
                    </div>
                </a>
            @else
                <div class="app-panel overflow-hidden px-4 py-4 sm:px-5">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-[1.2rem] bg-[radial-gradient(circle_at_top,_#67e8f9,_#38bdf8_45%,_#2563eb_100%)] text-lg font-black text-white shadow-[0_16px_34px_-18px_rgba(56,189,248,0.85)]">S</div>
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Workspace Aktif</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ '@'.$user->username }}</p>
                            <p class="text-xs text-slate-500">Siap untuk monitoring hari ini</p>
                        </div>
                    </div>
                </div>
            @endif
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
                <section class="app-panel app-animate-enter overflow-hidden p-3 sm:p-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="app-overline">Mode Dashboard</p>
                            <p class="mt-1 text-sm text-slate-600">Pindah dari view tim cabang ke aktivitas pribadi tanpa keluar halaman.</p>
                        </div>
                        <div class="inline-flex w-full rounded-[1.25rem] bg-slate-100/90 p-1 sm:w-auto">
                            <button type="button" @click="switchTab('branch')" :class="activeTab === 'branch' ? 'bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_14px_34px_-18px_rgba(29,78,216,0.75)]' : 'text-slate-500'" class="rounded-[1rem] px-4 py-2 text-sm font-semibold transition">Dashboard Cabang</button>
                            <button type="button" @click="switchTab('personal')" :class="activeTab === 'personal' ? 'bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_14px_34px_-18px_rgba(29,78,216,0.75)]' : 'text-slate-500'" class="rounded-[1rem] px-4 py-2 text-sm font-semibold transition">Aktivitas Saya</button>
                        </div>
                    </div>
                </section>
            @endif

            <section>
                <div class="app-panel app-animate-enter relative overflow-hidden p-5 sm:p-6">
                    <div class="absolute -right-10 top-4 h-28 w-28 rounded-full bg-sky-300/22 blur-3xl"></div>
                    <div class="absolute left-8 top-16 h-20 w-20 rounded-full bg-cyan-200/20 blur-3xl"></div>
                    <div class="relative">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="max-w-xl">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">Pulse Hari Ini</p>
                                <h3 class="mt-3 text-2xl font-semibold text-ink-950 sm:text-[2rem]" x-text="primaryMetric.value"></h3>
                                <p class="mt-2 text-sm font-semibold text-slate-800" x-text="primaryMetric.label"></p>
                                <p class="mt-2 text-sm leading-7 text-slate-600" x-text="primaryMetric.hint"></p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/70 bg-white/76 px-4 py-3 text-right shadow-[0_20px_40px_-28px_rgba(15,23,42,0.22)] backdrop-blur">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Scope Aktif</p>
                                <p class="mt-2 text-sm font-semibold text-slate-900" x-text="scopeTitle"></p>
                                <p class="mt-1 text-xs text-slate-500" x-text="current.chartHelper"></p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-3">
                            <template x-for="metric in supportingMetrics" :key="metric.label">
                                <div class="app-kpi app-card-interactive" :class="metricCardClass(metric.label)">
                                    <div class="absolute inset-x-4 top-0 h-1 rounded-b-full" :class="metricAccentClass(metric.label)"></div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400" x-text="metric.label"></p>
                                    <p class="mt-3 text-xl font-semibold" :class="metricValueClass(metric.label)" x-text="metric.value"></p>
                                    <p class="mt-2 text-xs leading-5 text-slate-500" x-text="metric.hint"></p>
                                </div>
                            </template>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <template x-for="highlight in current.highlights.slice(0, 3)" :key="highlight.label">
                                <span class="app-chip">
                                    <span class="font-bold text-slate-900" x-text="highlight.value"></span>
                                    <span x-text="highlight.label"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid items-start gap-6 xl:grid-cols-[1.1fr_0.9fr]">
                <div class="app-panel app-animate-enter p-5 sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Trend Kunjungan</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Kunjungan & Collection</h3>
                        </div>
                        <p class="text-sm text-slate-500" x-text="current.chartHelper"></p>
                    </div>
                    <div class="app-soft-panel mt-6 overflow-hidden p-4 sm:p-5">
                        <div class="mb-4 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                            <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 shadow-sm"><span class="app-status-dot bg-blue-600"></span>Kunjungan</span>
                            <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1.5 shadow-sm"><span class="app-status-dot bg-emerald-500"></span>Collection</span>
                        </div>
                        <div class="h-[16rem] sm:h-[18rem]">
                            <canvas id="dashboard-performance-chart"></canvas>
                        </div>
                    </div>
                </div>

                @if (! $user->isSales() && ! $user->isSmd())
                    <div class="space-y-6">
                        <div class="app-panel app-animate-enter p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-600">Customer Priority</p>
                                    <h3 class="mt-2 text-xl font-semibold text-ink-950">Top customer</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Order aktif</span>
                            </div>
                            <div class="mt-5 space-y-3">
                                <template x-if="current.topCustomers.length === 0">
                                    <div class="app-empty-state px-4 py-5">Belum ada customer dengan order tercatat.</div>
                                </template>
                                <template x-for="(customer, index) in current.topCustomers" :key="customer.id ?? customer.name ?? index">
                                    <div class="rounded-[1.5rem] border border-blue-100 bg-[linear-gradient(180deg,#f8fbff_0%,#eef6ff_100%)] px-4 py-3.5 shadow-[0_20px_40px_-32px_rgba(37,99,235,0.25)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_22px_46px_-30px_rgba(37,99,235,0.3)]">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-[1rem] text-sm font-semibold text-white shadow-[0_12px_28px_-18px_rgba(37,99,235,0.8)] ring-1 ring-blue-200/40" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                                    <span class="text-white" x-text="index + 1"></span>
                                                </span>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900" x-text="customer.name"></p>
                                                    <p class="mt-1 text-xs leading-5 text-slate-500"><span x-text="customer.total_orders"></span> order · terakhir <span x-text="formatSimpleDate(customer.last_order_at)"></span></p>
                                                </div>
                                            </div>
                                            <span class="inline-flex min-w-[5rem] items-center justify-center rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-blue-700 shadow-sm" x-text="formatCurrency(customer.total_amount)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="app-panel app-animate-enter p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">Follow Up Supervisor</p>
                                    <h3 class="mt-2 text-xl font-semibold text-ink-950">Lama tidak order</h3>
                                </div>
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">> 30 hari</span>
                            </div>
                            <div class="mt-5 space-y-3">
                                <template x-if="current.staleCustomers.length === 0">
                                    <div class="app-empty-state px-4 py-5">Belum ada customer yang melewati batas 30 hari tanpa order.</div>
                                </template>
                                <template x-for="customer in current.staleCustomers" :key="customer.id ?? customer.name">
                                    <div class="rounded-[1.5rem] border border-amber-100 bg-[linear-gradient(180deg,#fffaf0_0%,#fff5e6_100%)] px-4 py-3.5 shadow-[0_20px_40px_-32px_rgba(245,158,11,0.22)] transition duration-200 hover:-translate-y-1 hover:shadow-[0_22px_46px_-30px_rgba(245,158,11,0.28)]">
                                        <div class="flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900" x-text="customer.name"></p>
                                                <p class="mt-1 text-xs text-slate-500">Terakhir order <span x-text="formatSimpleDate(customer.last_order_at)"></span> · <span x-text="customer.total_orders"></span> order total</p>
                                            </div>
                                            <p class="inline-flex min-w-[5rem] items-center justify-center rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-amber-700 shadow-sm" x-text="daysSince(customer.last_order_at)"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                @endif
            </section>

            <section class="app-panel app-animate-enter overflow-hidden p-5 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="app-overline">Monitoring Operasional</p>
                        <h3 class="mt-2 text-xl font-semibold text-ink-950">Daftar Kunjungan</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="app-chip" x-text="`${current.recentVisits.length} visit ditampilkan`"></span>
                        <a href="{{ route('visit-history.index') }}" class="app-glass-button px-4 py-2.5">Buka History</a>
                    </div>
                </div>

                <div class="mt-5 hidden overflow-hidden rounded-[1.65rem] border border-slate-200/90 lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5fe_100%)] text-left text-slate-500">
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
                        <tbody class="divide-y divide-slate-100 bg-white/90">
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
                        <div class="app-soft-panel app-card-interactive px-4 py-4 text-sm text-slate-600">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900" x-text="visit.outlet?.name || '-' "></p>
                                    <p class="mt-1 text-xs text-slate-500" x-text="`${String(visit.visit_type).toUpperCase()} · ${visit.user?.name || '-'}`"></p>
                                </div>
                                <p class="text-xs text-slate-400" x-text="formatVisitDate(visit.visited_at, visit.branch?.timezone)"></p>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Sales Amount</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="formatCurrency(visitSalesAmount(visit))"></p>
                                </div>
                                <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-400">Collection</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-900" x-text="formatCurrency(visitCollection(visit))"></p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold" :class="visit.outlet_condition === 'buka' ? 'bg-emerald-50 text-emerald-700' : (visit.outlet_condition === 'order_by_wa' ? 'bg-violet-50 text-violet-700' : 'bg-amber-50 text-amber-700')" x-text="formatOutletCondition(visit.outlet_condition)"></span>
                                <a :href="`/visit-history/${visit.id}`" class="inline-flex items-center rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-900 shadow-sm shadow-sky-100/50">Detail</a>
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
                    get primaryMetric() {
                        return this.current?.metrics?.[1] || this.current?.metrics?.[0] || { label: '-', value: '-', hint: '' };
                    },
                    get supportingMetrics() {
                        const metrics = this.current?.metrics || [];

                        return [metrics[0], ...metrics.slice(2)].filter(Boolean);
                    },
                    get scopeTitle() {
                        if (this.activeTab === 'personal') {
                            return 'Aktivitas pribadi';
                        }

                        if (this.activeTab === 'branch') {
                            return `Cabang ${this.branchName}`;
                        }

                        return this.branchName;
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

                        return 'slate';
                    },
                    metricCardClass(label) {
                        return {
                            blue: 'border-blue-100 bg-[linear-gradient(180deg,#fbfdff_0%,#eef6ff_100%)]',
                            emerald: 'border-emerald-100 bg-[linear-gradient(180deg,#fbfffd_0%,#ecfdf5_100%)]',
                            amber: 'border-amber-100 bg-[linear-gradient(180deg,#fffdf8_0%,#fff7ed_100%)]',
                            slate: 'border-white/70 bg-white/90',
                        }[this.metricTone(label)];
                    },
                    metricAccentClass(label) {
                        return {
                            blue: 'bg-[linear-gradient(90deg,#60a5fa_0%,#2563eb_100%)]',
                            emerald: 'bg-[linear-gradient(90deg,#6ee7b7_0%,#10b981_100%)]',
                            amber: 'bg-[linear-gradient(90deg,#fcd34d_0%,#f59e0b_100%)]',
                            slate: 'bg-[linear-gradient(90deg,#cbd5e1_0%,#94a3b8_100%)]',
                        }[this.metricTone(label)];
                    },
                    metricValueClass(label) {
                        return {
                            blue: 'text-blue-700',
                            emerald: 'text-emerald-700',
                            amber: 'text-amber-700',
                            slate: 'text-ink-950',
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
