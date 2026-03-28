<x-app-layout>
    @php($user = auth()->user())
    @php($branchName = $user->branch?->name ?? 'Semua Cabang')
    @php($headline = match ($user->role) {
        \App\Models\User::ROLE_ADMIN_PUSAT => 'Monitor semua cabang, outlet baru, dan performa tim dari satu dashboard pusat.',
        \App\Models\User::ROLE_SUPERVISOR => 'Pantau aktivitas cabang, verifikasi outlet baru, dan tetap bisa turun kunjungan atas nama sendiri.',
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

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ([
                    ['label' => 'Visit Hari Ini', 'value' => '128', 'hint' => 'Simulasi KPI kunjungan harian'],
                    ['label' => 'Outlet Pending', 'value' => '18', 'hint' => 'Menunggu verifikasi supervisor'],
                    ['label' => 'Prospek Follow Up', 'value' => '42', 'hint' => 'Siap ditindaklanjuti cabang'],
                    ['label' => 'NOO Tanpa Official Kode', 'value' => '9', 'hint' => 'Perlu update official kode'],
                ] as $metric)
                    <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $metric['label'] }}</p>
                        <p class="mt-4 text-3xl font-semibold text-ink-950">{{ $metric['value'] }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $metric['hint'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Trend Performance</p>
                            <h3 class="mt-2 text-xl font-semibold text-ink-950">Aktivitas 7 hari terakhir</h3>
                        </div>
                        <p class="text-sm text-slate-500">Chart.js seed chart untuk fondasi dashboard</p>
                    </div>

                    <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4">
                        <canvas id="dashboard-performance-chart" height="120"></canvas>
                    </div>
                </div>

                <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Aksi Berikutnya</p>
                    <div class="mt-4 space-y-3">
                        @foreach ([
                            'Bangun tabel branches, users, outlets, visits sesuai blueprint docs.',
                            'Tambahkan branch scoping dan redirect berbasis role pada app shell.',
                            'Mulai form kunjungan sales dengan outlet autocomplete tanpa reload.',
                        ] as $step)
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
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Documentation Sync</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <p>Semua keputusan auth dan fondasi role akan terus di-update di `docs/` agar mudah dilanjutkan model AI lain.</p>
                        <a href="{{ url('/') }}#docs" class="inline-flex items-center rounded-2xl bg-ink-950 px-4 py-2 font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800">
                            Lihat overview dokumentasi
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const chartElement = document.getElementById('dashboard-performance-chart');

                if (! chartElement || typeof window.Chart === 'undefined') {
                    return;
                }

                new window.Chart(chartElement, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Visit',
                            data: [12, 18, 16, 23, 21, 28, 24],
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
                        plugins: {
                            legend: { display: false },
                        },
                    },
                });
            });
        </script>
    @endpush
</x-app-layout>
