<x-app-layout>
    @php
        $typeConfig = [
            'sales' => ['label' => 'Sales', 'icon' => 'S', 'tone' => 'blue'],
            'smd' => ['label' => 'SMD', 'icon' => 'M', 'tone' => 'emerald'],
            'outlets' => ['label' => 'Outlet', 'icon' => 'O', 'tone' => 'amber'],
        ][$activeType];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="app-chip">Laporan</span>
                    <span class="app-chip">{{ $typeConfig['label'] }}</span>
                </div>
                <h2 class="mt-4 text-3xl font-semibold leading-tight text-ink-950">{{ $title }}</h2>
            </div>
            <a href="{{ route('reports.export', ['type' => $activeType, 'from' => $filters['from'], 'to' => $filters['to'], 'branch_id' => $filters['branchId'] ?? null, 'user_id' => $filters['userId'] ?? null]) }}" class="inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] px-5 py-3 text-sm font-semibold text-white shadow-[0_20px_40px_-18px_rgba(29,78,216,0.75)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_46px_-18px_rgba(29,78,216,0.9)]">Export CSV</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="app-panel app-animate-enter overflow-hidden p-4 sm:p-6">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                    <div class="max-w-3xl">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-[1.2rem] bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-sm font-semibold text-white shadow-[0_18px_38px_-20px_rgba(29,78,216,0.75)]">{{ $typeConfig['icon'] }}</div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Mode Laporan</p>
                                <h3 class="mt-1 text-xl font-semibold text-ink-950">{{ $typeConfig['label'] }}</h3>
                            </div>
                        </div>
                        <p class="mt-4 text-sm leading-7 text-slate-600">{{ $description }}</p>
                    </div>

                    <div class="grid grid-cols-3 gap-2 sm:gap-3">
                        <a href="{{ route('reports.index', ['type' => 'sales', 'from' => $filters['from'], 'to' => $filters['to'], 'branch_id' => $filters['branchId'] ?? null, 'user_id' => $filters['userId'] ?? null]) }}" class="rounded-[1.2rem] px-4 py-3 text-center text-sm font-semibold transition {{ $activeType === 'sales' ? 'bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_16px_34px_-18px_rgba(29,78,216,0.75)]' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}">Sales</a>
                        <a href="{{ route('reports.index', ['type' => 'smd', 'from' => $filters['from'], 'to' => $filters['to'], 'branch_id' => $filters['branchId'] ?? null, 'user_id' => $filters['userId'] ?? null]) }}" class="rounded-[1.2rem] px-4 py-3 text-center text-sm font-semibold transition {{ $activeType === 'smd' ? 'bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_16px_34px_-18px_rgba(29,78,216,0.75)]' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}">SMD</a>
                        <a href="{{ route('reports.index', ['type' => 'outlets', 'from' => $filters['from'], 'to' => $filters['to'], 'branch_id' => $filters['branchId'] ?? null, 'user_id' => $filters['userId'] ?? null]) }}" class="rounded-[1.2rem] px-4 py-3 text-center text-sm font-semibold transition {{ $activeType === 'outlets' ? 'bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] text-white shadow-[0_16px_34px_-18px_rgba(29,78,216,0.75)]' : 'border border-slate-200 bg-white text-slate-700 hover:border-slate-300' }}">Outlet</a>
                    </div>
                </div>
            </section>

            <section class="app-panel app-animate-enter p-4 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Filter</p>
                        <h3 class="mt-2 text-xl font-semibold text-ink-950">Atur rentang dan pelaksana laporan</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="app-chip">{{ $filters['from'] }}</span>
                        <span class="app-chip">{{ $filters['to'] }}</span>
                    </div>
                </div>

                <form method="GET" class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                    <input type="hidden" name="type" value="{{ $activeType }}">
                    <div>
                        <x-input-label for="from" value="Dari tanggal" />
                        <x-text-input id="from" name="from" type="date" class="mt-2 block w-full" :value="$filters['from']" />
                    </div>
                    <div>
                        <x-input-label for="to" value="Sampai tanggal" />
                        <x-text-input id="to" name="to" type="date" class="mt-2 block w-full" :value="$filters['to']" />
                    </div>
                    <div>
                        <x-input-label for="branch_id" value="Cabang" />
                        <select id="branch_id" name="branch_id" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">Semua Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((int) ($filters['branchId'] ?? 0) === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="user_id" value="User" />
                        <select id="user_id" name="user_id" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">Semua User</option>
                            @foreach ($users as $reportUser)
                                <option value="{{ $reportUser->id }}" @selected((int) ($filters['userId'] ?? 0) === $reportUser->id)>{{ $reportUser->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 xl:col-span-2 flex flex-col gap-3 sm:flex-row sm:items-end">
                        <x-primary-button class="justify-center sm:min-w-[180px]">Terapkan</x-primary-button>
                        <a href="{{ route('reports.index', ['type' => $activeType]) }}" class="app-glass-button justify-center sm:min-w-[140px]">Reset</a>
                    </div>
                </form>
            </section>

            <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 {{ count($summary) >= 5 ? '2xl:grid-cols-5' : '' }}">
                @foreach ($summary as $item)
                    <div class="app-panel app-animate-enter p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $item['label'] }}</p>
                        <p class="mt-4 text-2xl font-semibold text-ink-950">{{ $item['value'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="app-panel app-animate-enter p-4 sm:p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Data Laporan</p>
                        <h3 class="mt-2 text-xl font-semibold text-ink-950">Daftar hasil sesuai filter</h3>
                    </div>
                    <span class="app-chip">{{ $rows->total() }} data</span>
                </div>

                <div class="mt-5 hidden overflow-hidden rounded-[1.65rem] border border-slate-200/90 lg:block">
                    @if ($activeType === 'sales')
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5fe_100%)] text-left text-slate-500">
                                <tr>
                                    <th class="px-4 py-3.5 font-semibold">Waktu</th>
                                    <th class="px-4 py-3.5 font-semibold">Outlet</th>
                                    <th class="px-4 py-3.5 font-semibold">Pelaksana</th>
                                    <th class="px-4 py-3.5 font-semibold">Order</th>
                                    <th class="px-4 py-3.5 font-semibold">Tagihan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white/90">
                                @forelse ($rows as $row)
                                    <tr class="transition duration-200 hover:bg-sky-50/60">
                                        <td class="px-4 py-4 text-slate-600">{{ $row->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-4 text-slate-900">{{ $row->outlet?->name }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $row->user?->name }}</td>
                                        <td class="px-4 py-4 text-slate-900">Rp {{ number_format((float) ($row->salesDetail?->order_amount ?? 0), 0, ',', '.') }}</td>
                                        <td class="px-4 py-4 text-slate-900">Rp {{ number_format((float) ($row->salesDetail?->receivable_amount ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data laporan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @elseif ($activeType === 'smd')
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5fe_100%)] text-left text-slate-500">
                                <tr>
                                    <th class="px-4 py-3.5 font-semibold">Waktu</th>
                                    <th class="px-4 py-3.5 font-semibold">Outlet</th>
                                    <th class="px-4 py-3.5 font-semibold">Aktivitas</th>
                                    <th class="px-4 py-3.5 font-semibold">PO</th>
                                    <th class="px-4 py-3.5 font-semibold">Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white/90">
                                @forelse ($rows as $row)
                                    <tr class="transition duration-200 hover:bg-sky-50/60">
                                        <td class="px-4 py-4 text-slate-600">{{ $row->visitedAtForBranch()?->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-4 text-slate-900">{{ $row->outlet?->name }}</td>
                                        <td class="px-4 py-4 text-slate-600">
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach ($row->smdActivities as $activity)
                                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ str($activity->activity_type)->replace('_', ' ')->title() }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-slate-900">Rp {{ number_format((float) ($row->smdDetail?->po_amount ?? 0), 0, ',', '.') }}</td>
                                        <td class="px-4 py-4 text-slate-900">Rp {{ number_format((float) ($row->smdDetail?->payment_amount ?? 0), 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data laporan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5fe_100%)] text-left text-slate-500">
                                <tr>
                                    <th class="px-4 py-3.5 font-semibold">Dibuat</th>
                                    <th class="px-4 py-3.5 font-semibold">Outlet</th>
                                    <th class="px-4 py-3.5 font-semibold">Jenis</th>
                                    <th class="px-4 py-3.5 font-semibold">Status</th>
                                    <th class="px-4 py-3.5 font-semibold">Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white/90">
                                @forelse ($rows as $row)
                                    <tr class="transition duration-200 hover:bg-sky-50/60">
                                        <td class="px-4 py-4 text-slate-600">{{ $row->created_at?->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-4 text-slate-900">{{ $row->name }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $row->typeLabel() }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $row->statusLabel() }}</td>
                                        <td class="px-4 py-4 text-slate-600">{{ $row->verificationLabel() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada data laporan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="mt-5 space-y-3 lg:hidden">
                    @forelse ($rows as $row)
                        <div class="app-soft-panel app-card-interactive px-4 py-4 text-sm text-slate-600">
                            @if ($activeType === 'outlets')
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $row->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $row->created_at?->format('d M Y H:i') }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm">{{ $row->typeLabel() }}</span>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Status</p>
                                        <p class="mt-1 font-semibold text-slate-900">{{ $row->statusLabel() }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Verifikasi</p>
                                        <p class="mt-1 font-semibold text-slate-900">{{ $row->verificationLabel() }}</p>
                                    </div>
                                </div>
                            @elseif ($activeType === 'smd')
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $row->outlet?->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $row->visitedAtForBranch()?->format('d M Y H:i') }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">SMD</span>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @foreach ($row->smdActivities as $activity)
                                        <span class="inline-flex rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm">{{ str($activity->activity_type)->replace('_', ' ')->title() }}</span>
                                    @endforeach
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">PO</p>
                                        <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format((float) ($row->smdDetail?->po_amount ?? 0), 0, ',', '.') }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Pembayaran</p>
                                        <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format((float) ($row->smdDetail?->payment_amount ?? 0), 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $row->outlet?->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $row->visitedAtForBranch()?->format('d M Y H:i') }} · {{ $row->user?->name }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Sales</span>
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Order</p>
                                        <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format((float) ($row->salesDetail?->order_amount ?? 0), 0, ',', '.') }}</p>
                                    </div>
                                    <div class="rounded-2xl bg-white px-3 py-3 shadow-sm">
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-slate-400">Tagihan</p>
                                        <p class="mt-1 font-semibold text-slate-900">Rp {{ number_format((float) ($row->salesDetail?->receivable_amount ?? 0), 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada data laporan.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $rows->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
