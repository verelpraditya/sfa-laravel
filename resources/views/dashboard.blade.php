<x-app-layout>
    @php($user = auth()->user())
    @php($branchName = $user->branch?->name ?? 'Semua Cabang')
    @php($headline = match ($user->role) {
        \App\Models\User::ROLE_ADMIN_PUSAT => 'Monitor semua cabang, outlet baru, dan performa tim dari satu dashboard pusat.',
        \App\Models\User::ROLE_SUPERVISOR => 'Pantau aktivitas cabang dan buka tab aktivitas saya saat supervisor juga turun kunjungan sendiri.',
        \App\Models\User::ROLE_SMD => 'Lihat ringkasan aktivitas SMD dan lanjutkan workflow PO, display, tukar faktur, atau tagihan.',
        default => 'Buka kunjungan sales, cari outlet dengan cepat, dan pantau hasil kerja harian kamu.',
    })

    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $user->roleLabel() }}</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Dashboard {{ $branchName }}</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">{{ $headline }}</p>
            </div>
            <div class="rounded-2xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700 shadow-sm">
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
                <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-3 shadow-sm shadow-slate-200/60 sm:p-4">
                    <div class="inline-flex w-full rounded-[1.25rem] bg-slate-100 p-1 sm:w-auto">
                        <button type="button" @click="switchTab('branch')" :class="activeTab === 'branch' ? 'bg-white text-ink-950 shadow-sm' : 'text-slate-500'" class="rounded-[1rem] px-4 py-2 text-sm font-semibold transition">
                            Dashboard Cabang
                        </button>
                        <button type="button" @click="switchTab('personal')" :class="activeTab === 'personal' ? 'bg-white text-ink-950 shadow-sm' : 'text-slate-500'" class="rounded-[1rem] px-4 py-2 text-sm font-semibold transition">
                            Aktivitas Saya
                        </button>
                    </div>
                </section>
            @endif

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <template x-for="metric in current.metrics" :key="metric.label">
                    <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400" x-text="metric.label"></p>
                        <p class="mt-4 text-3xl font-semibold text-ink-950" x-text="metric.value"></p>
                        <p class="mt-2 text-sm text-slate-500" x-text="metric.hint"></p>
                    </div>
                </template>
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Trend Performance</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950" x-text="current.chartContext"></h3>
                        </div>
                        <p class="text-sm text-slate-500" x-text="current.chartHelper"></p>
                    </div>

                    <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                        <canvas id="dashboard-performance-chart" height="120"></canvas>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Aksi Berikutnya</p>
                    <div class="mt-4 space-y-3">
                        @foreach (['Supervisor bisa verifikasi outlet dan lengkapi official kode tanpa mengubah data visit.', 'Dashboard sekarang memakai agregasi real dari outlet dan visit yang ada di database.', 'Langkah berikutnya adalah merapikan laporan dan approval flow tambahan jika dibutuhkan.'] as $step)
                            <div class="flex gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="mt-1 h-2.5 w-2.5 rounded-full bg-brand-500"></div>
                                <p class="text-sm leading-6 text-slate-600">{{ $step }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Role Scope</p>
                    <div class="mt-4 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 text-sm leading-7 text-slate-600">
                        <p><span class="font-semibold text-slate-900">Role:</span> {{ $user->roleLabel() }}</p>
                        <p><span class="font-semibold text-slate-900">Branch:</span> {{ $branchName }}</p>
                        <p><span class="font-semibold text-slate-900">Status:</span> {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Pending Outlet Terbaru</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <template x-if="current.recentPendingOutlets.length === 0">
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Tidak ada outlet pending saat ini.</div>
                        </template>
                        <template x-for="item in current.recentPendingOutlets" :key="item.id">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <p class="font-semibold text-slate-900" x-text="item.name"></p>
                                <p class="mt-1 text-xs text-slate-500" x-text="`${item.district}, ${item.city} · ${formatType(item.outlet_type)}`"></p>
                            </div>
                        </template>
                    </div>
                </div>
            </section>

            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Aktivitas Terbaru</p>
                        <h3 class="mt-2 text-xl font-semibold text-ink-950" x-text="activeTab === 'personal' ? 'Visit supervisor terbaru' : 'Visit terbaru di scope kamu'"></h3>
                    </div>
                    <p class="text-sm text-slate-500">Tampil otomatis dari data visit yang sudah masuk</p>
                </div>

                <div class="mt-5 grid gap-3 lg:grid-cols-2">
                    <template x-if="current.recentVisits.length === 0">
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Belum ada aktivitas yang tercatat di scope ini.</div>
                    </template>
                    <template x-for="visit in current.recentVisits" :key="visit.id">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900" x-text="visit.outlet?.name || '-' "></p>
                                    <p class="mt-1 text-xs text-slate-500" x-text="`${String(visit.visit_type).toUpperCase()} · ${visit.user?.name || '-'}`"></p>
                                </div>
                                <p class="text-xs text-slate-400" x-text="formatVisitDate(visit.visited_at)"></p>
                            </div>
                            <p class="mt-3" x-text="visit.notes || 'Tanpa catatan tambahan.'"></p>
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
                                datasets: [{
                                    label: 'Visit',
                                    data: this.current.chartValues,
                                    borderColor: '#2563eb',
                                    backgroundColor: 'rgba(37, 99, 235, 0.14)',
                                    tension: 0.35,
                                    fill: true,
                                    pointRadius: 0,
                                }],
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: { grid: { display: false } },
                                    y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.12)' } },
                                },
                                plugins: { legend: { display: false } },
                            },
                        });
                    },
                    formatType(type) {
                        return String(type || '').replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase());
                    },
                    formatVisitDate(value) {
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
                        }).format(date);
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
