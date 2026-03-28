<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Laporan</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">{{ $title }}</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">{{ $description }}</p>
            </div>
            <a href="{{ route('reports.export', ['type' => $activeType, 'from' => $filters['from'], 'to' => $filters['to']]) }}" class="inline-flex items-center justify-center rounded-2xl bg-ink-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-900/15 transition hover:bg-slate-800">Export CSV</a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('reports.index', ['type' => 'sales', 'from' => $filters['from'], 'to' => $filters['to']]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $activeType === 'sales' ? 'bg-ink-950 text-white' : 'bg-slate-100 text-slate-600' }}">Sales</a>
                    <a href="{{ route('reports.index', ['type' => 'smd', 'from' => $filters['from'], 'to' => $filters['to']]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $activeType === 'smd' ? 'bg-ink-950 text-white' : 'bg-slate-100 text-slate-600' }}">SMD</a>
                    <a href="{{ route('reports.index', ['type' => 'outlets', 'from' => $filters['from'], 'to' => $filters['to']]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ $activeType === 'outlets' ? 'bg-ink-950 text-white' : 'bg-slate-100 text-slate-600' }}">Outlet</a>
                </div>
                <form method="GET" class="mt-5 grid gap-3 md:grid-cols-4">
                    <input type="hidden" name="type" value="{{ $activeType }}">
                    <div>
                        <x-input-label for="from" value="Dari tanggal" />
                        <x-text-input id="from" name="from" type="date" class="mt-2 block w-full" :value="$filters['from']" />
                    </div>
                    <div>
                        <x-input-label for="to" value="Sampai tanggal" />
                        <x-text-input id="to" name="to" type="date" class="mt-2 block w-full" :value="$filters['to']" />
                    </div>
                    <div class="md:col-span-2 flex items-end gap-3">
                        <x-primary-button>Terapkan</x-primary-button>
                        <a href="{{ route('reports.index', ['type' => $activeType]) }}" class="inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-200/60 transition hover:border-slate-300 hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                @foreach ($summary as $item)
                    <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $item['label'] }}</p>
                        <p class="mt-4 text-2xl font-semibold text-ink-950">{{ $item['value'] }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block">
                    @if ($activeType === 'sales')
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-3 font-semibold">Waktu</th><th class="px-4 py-3 font-semibold">Outlet</th><th class="px-4 py-3 font-semibold">Pelaksana</th><th class="px-4 py-3 font-semibold">Order</th><th class="px-4 py-3 font-semibold">Tagihan</th></tr></thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($rows as $row)
                                    <tr><td class="px-4 py-4 text-slate-600">{{ $row->visitedAtForBranch()?->format('d M Y H:i') }}</td><td class="px-4 py-4 text-slate-600">{{ $row->outlet?->name }}</td><td class="px-4 py-4 text-slate-600">{{ $row->user?->name }}</td><td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) ($row->salesDetail?->order_amount ?? 0), 0, ',', '.') }}</td><td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) ($row->salesDetail?->receivable_amount ?? 0), 0, ',', '.') }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data laporan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @elseif ($activeType === 'smd')
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-3 font-semibold">Waktu</th><th class="px-4 py-3 font-semibold">Outlet</th><th class="px-4 py-3 font-semibold">Aktivitas</th><th class="px-4 py-3 font-semibold">PO</th><th class="px-4 py-3 font-semibold">Pembayaran</th></tr></thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($rows as $row)
                                    <tr><td class="px-4 py-4 text-slate-600">{{ $row->visitedAtForBranch()?->format('d M Y H:i') }}</td><td class="px-4 py-4 text-slate-600">{{ $row->outlet?->name }}</td><td class="px-4 py-4 text-slate-600">{{ $row->smdActivities->pluck('activity_type')->implode(', ') }}</td><td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) ($row->smdDetail?->po_amount ?? 0), 0, ',', '.') }}</td><td class="px-4 py-4 text-slate-600">Rp {{ number_format((float) ($row->smdDetail?->payment_amount ?? 0), 0, ',', '.') }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data laporan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @else
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 text-left text-slate-500"><tr><th class="px-4 py-3 font-semibold">Dibuat</th><th class="px-4 py-3 font-semibold">Outlet</th><th class="px-4 py-3 font-semibold">Jenis</th><th class="px-4 py-3 font-semibold">Status</th><th class="px-4 py-3 font-semibold">Verifikasi</th></tr></thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse ($rows as $row)
                                    <tr><td class="px-4 py-4 text-slate-600">{{ $row->created_at?->format('d M Y H:i') }}</td><td class="px-4 py-4 text-slate-600">{{ $row->name }}</td><td class="px-4 py-4 text-slate-600">{{ $row->typeLabel() }}</td><td class="px-4 py-4 text-slate-600">{{ $row->statusLabel() }}</td><td class="px-4 py-4 text-slate-600">{{ $row->verificationLabel() }}</td></tr>
                                @empty
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada data laporan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($rows as $row)
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                            @if ($activeType === 'outlets')
                                <p class="font-semibold text-slate-900">{{ $row->name }}</p>
                                <p class="mt-1">{{ $row->typeLabel() }} · {{ $row->statusLabel() }}</p>
                            @else
                                <p class="font-semibold text-slate-900">{{ $row->outlet?->name }}</p>
                                <p class="mt-1">{{ $row->visitedAtForBranch()?->format('d M Y H:i') }}</p>
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
