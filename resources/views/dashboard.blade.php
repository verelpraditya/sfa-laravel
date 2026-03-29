<x-app-layout>
    @php($user = auth()->user())
    @php($branchName = $user->branch?->name ?? 'Semua Cabang')
    @php($headline = match ($user->role) {
        \App\Models\User::ROLE_ADMIN_PUSAT => 'Monitor visit tim, sales amount, collection, dan jumlah PO lintas cabang dari satu dashboard pusat.',
        \App\Models\User::ROLE_SUPERVISOR => 'Pantau visit tim hari ini, sales amount, collection, jumlah PO, dan monitoring operasional cabang dalam satu layar.',
        \App\Models\User::ROLE_SMD => 'Lihat nominal PO, collection, dan aktivitas lapangan yang sudah berjalan.',
        default => 'Pantau sales amount, collection, dan daftar kunjungan sales kamu dengan cepat.',
    })

    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $user->roleLabel() }}</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Dashboard {{ $branchName }}</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">{{ $headline }}</p>
            </div>
            <div class="rounded-2xl border border-sky-100 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-800 shadow-sm">
                Login sebagai {{ '@'.$user->username }}
            </div>
        </div>
    </x-slot>

    @php($defaultDashboard = $dashboardData)

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8"
            x-data="dashboardView({
                activeTab: '{{ $user->isSupervisor() ? 'branch' : 'default' }}',
                datasets: {
                    default: @js($defaultDashboard),
                    branch: @js($supervisorBranchData),
                    personal: @js($supervisorPersonalData),
                }
            })"
            x-init="initChart()"
        >
            @if ($user->isSupervisor())
                <section class="app-panel p-3 sm:p-4">
                    <div class="inline-flex w-full rounded-[1.25rem] bg-slate-100/90 p-1 sm:w-auto">
                        <button type="button" @click="switchTab('branch')" :class="activeTab === 'branch' ? 'bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_14px_34px_-18px_rgba(29,78,216,0.75)]' : 'text-slate-500'" class="rounded-[1rem] px-4 py-2 text-sm font-semibold transition">Dashboard Cabang</button>
                        <button type="button" @click="switchTab('personal')" :class="activeTab === 'personal' ? 'bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_14px_34px_-18px_rgba(29,78,216,0.75)]' : 'text-slate-500'" class="rounded-[1rem] px-4 py-2 text-sm font-semibold transition">Aktivitas Saya</button>
                    </div>
                </section>
            @endif

            <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <div class="app-panel p-5">
                    <div class="rounded-[1.7rem] bg-[linear-gradient(135deg,#1d4ed8_0%,#60a5fa_100%)] p-6 text-white shadow-[0_24px_60px_-28px_rgba(29,78,216,0.7)]">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-100/90" x-text="current.metrics[1]?.label ?? 'Sales Amount Hari Ini'"></p>
                        <p class="mt-4 text-4xl font-semibold leading-none" x-text="current.metrics[1]?.value ?? '-' "></p>
                        <p class="mt-4 text-sm text-sky-50/90">{{ $branchName }}</p>
                    </div>

                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <template x-if="current.metrics[0]">
                            <div class="app-soft-panel px-4 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400" x-text="current.metrics[0].label"></p>
                                <p class="mt-3 text-xl font-semibold text-ink-950" x-text="current.metrics[0].value"></p>
                                <p class="mt-1 text-xs text-slate-500" x-text="current.metrics[0].hint"></p>
                            </div>
                        </template>
                        <template x-for="metric in current.metrics.slice(2)" :key="metric.label">
                            <div class="app-soft-panel px-4 py-4">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-400" x-text="metric.label"></p>
                                <p class="mt-3 text-xl font-semibold text-ink-950" x-text="metric.value"></p>
                                <p class="mt-1 text-xs text-slate-500" x-text="metric.hint"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="app-panel p-5">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Trend Kunjungan</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Kunjungan & Collection</h3>
                        </div>
                        <p class="text-sm text-slate-500" x-text="current.chartHelper"></p>
                    </div>
                    <div class="app-soft-panel mt-6 p-4">
                        <canvas id="dashboard-performance-chart" height="120"></canvas>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
                <div class="app-panel p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Monitoring Operasional</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Perlu perhatian segera</h3>
                        </div>
                    </div>
                    <div class="mt-5 space-y-3">
                        <template x-for="item in current.highlights" :key="item.label">
                            <div class="app-soft-panel px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900" x-text="item.label"></p>
                                        <p class="mt-1 text-xs text-slate-500" x-text="item.hint"></p>
                                    </div>
                                    <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-sky-900" x-text="item.value"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="app-panel p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Top Performer</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Kontributor visit tertinggi</h3>
                        </div>
                    </div>
                    <div class="mt-5 space-y-3">
                        <template x-if="current.topPerformers.length === 0">
                            <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Belum ada cukup data untuk ranking.</div>
                        </template>
                        <template x-for="(performer, index) in current.topPerformers" :key="performer.name + index">
                            <div class="app-soft-panel px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-sm font-semibold text-white" x-text="index + 1"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900" x-text="performer.name"></p>
                                            <p class="mt-1 text-xs text-slate-500">Ranking berdasarkan jumlah visit pada scope aktif</p>
                                        </div>
                                    </div>
                                    <p class="text-sm font-semibold text-sky-900"><span x-text="performer.total_visits"></span> visit</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <section class="app-panel p-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Monitoring Operasional</p>
                        <h3 class="mt-2 text-xl font-semibold text-ink-950">Daftar Kunjungan</h3>
                    </div>
                    <p class="text-sm text-slate-500">Visit terbaru dengan konteks user, outlet, tipe, dan nominal</p>
                </div>

                <div class="mt-5 hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Waktu</th>
                                <th class="px-4 py-3 font-semibold">User</th>
                                <th class="px-4 py-3 font-semibold">Outlet</th>
                                <th class="px-4 py-3 font-semibold">Tipe</th>
                                <th class="px-4 py-3 font-semibold">Kondisi</th>
                                <th class="px-4 py-3 font-semibold">Sales Amount</th>
                                <th class="px-4 py-3 font-semibold">Collection</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="visit in current.recentVisits" :key="visit.id">
                                <tr>
                                    <td class="px-4 py-4 text-slate-600" x-text="formatVisitDate(visit.visited_at, visit.branch?.timezone)"></td>
                                    <td class="px-4 py-4 text-slate-900" x-text="visit.user?.name || '-' "></td>
                                    <td class="px-4 py-4 text-slate-600" x-text="visit.outlet?.name || '-' "></td>
                                    <td class="px-4 py-4 text-slate-600" x-text="String(visit.visit_type).toUpperCase()"></td>
                                    <td class="px-4 py-4 text-slate-600" x-text="visit.outlet_condition || '-' "></td>
                                    <td class="px-4 py-4 text-slate-900" x-text="formatCurrency(visitSalesAmount(visit))"></td>
                                    <td class="px-4 py-4 text-slate-900" x-text="formatCurrency(visitCollection(visit))"></td>
                                    <td class="px-4 py-4"><a :href="`/visit-history/${visit.id}`" class="inline-flex items-center rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-900 shadow-sm shadow-sky-100/50">Detail</a></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 space-y-3 lg:hidden">
                    <template x-if="current.recentVisits.length === 0">
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Belum ada aktivitas yang tercatat di scope ini.</div>
                    </template>
                    <template x-for="visit in current.recentVisits" :key="visit.id">
                        <div class="app-soft-panel px-4 py-4 text-sm text-slate-600">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900" x-text="visit.outlet?.name || '-' "></p>
                                    <p class="mt-1 text-xs text-slate-500" x-text="`${String(visit.visit_type).toUpperCase()} · ${visit.user?.name || '-'}`"></p>
                                </div>
                                <p class="text-xs text-slate-400" x-text="formatVisitDate(visit.visited_at, visit.branch?.timezone)"></p>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600" x-text="visit.outlet_condition || '-' "></span>
                                <span class="text-sm font-semibold text-slate-900" x-text="formatCurrency(visitCollection(visit))"></span>
                            </div>
                            <a :href="`/visit-history/${visit.id}`" class="mt-3 inline-flex items-center rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-900 shadow-sm shadow-sky-100/50">Detail</a>
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
                    chart: null,
                    get current() {
                        return this.datasets[this.activeTab] || this.datasets.default;
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
                                        tension: 0.35,
                                        fill: false,
                                        pointRadius: 3,
                                        pointHoverRadius: 5,
                                        yAxisID: 'yVisits',
                                    },
                                    {
                                        label: 'Collection',
                                        data: this.current.collectionValues || [],
                                        borderColor: '#22c55e',
                                        backgroundColor: 'rgba(34, 197, 94, 0.12)',
                                        tension: 0.35,
                                        fill: false,
                                        pointRadius: 3,
                                        pointHoverRadius: 5,
                                        yAxisID: 'yCollection',
                                    }
                                ],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        grid: { display: false },
                                    },
                                    yVisits: {
                                        type: 'linear',
                                        position: 'left',
                                        beginAtZero: true,
                                        grid: { color: 'rgba(148, 163, 184, 0.12)' },
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
                                        display: true,
                                        position: 'top',
                                        align: 'start',
                                        labels: {
                                            usePointStyle: true,
                                            boxWidth: 8,
                                            color: '#475569',
                                        },
                                    },
                                    tooltip: {
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
