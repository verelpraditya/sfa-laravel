<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Admin Pusat</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-slate-900">Master Cabang</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">Kelola cabang, kota, dan timezone agar jam kunjungan mengikuti lokasi cabang.</p>
            </div>
            <a href="{{ route('branches.create') }}" class="app-action-primary px-5 py-3">Tambah Cabang</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="app-panel p-5">
                <form method="GET" class="grid gap-3 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Cari cabang" />
                        <x-text-input id="search" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Nama, kode, kota, timezone" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="app-select mt-2 block w-full">
                            <option value="">Semua</option>
                            <option value="active" @selected($filters['status'] === 'active')>Active</option>
                            <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="md:col-span-4 flex flex-wrap gap-3">
                        <x-primary-button>Terapkan Filter</x-primary-button>
                        <a href="{{ route('branches.index') }}" class="app-action-secondary px-5 py-3">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
                <div class="hidden overflow-hidden rounded-lg border border-slate-200 lg:block">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Cabang</th>
                                <th class="px-4 py-3 font-semibold">Kota</th>
                                <th class="px-4 py-3 font-semibold">Timezone</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($branches as $branch)
                                <tr>
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-slate-900">{{ $branch->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $branch->code }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $branch->city }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $timezones[$branch->timezone] ?? $branch->timezone }}</td>
                                    <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $branch->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $branch->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td class="px-4 py-4"><a href="{{ route('branches.edit', $branch) }}" class="app-btn-sm px-4 py-2">Edit</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada cabang yang terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($branches as $branch)
                        <div class="app-soft-panel p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $branch->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $branch->code }} · {{ $branch->city }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $branch->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $branch->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <p class="mt-4 text-sm text-slate-600">{{ $timezones[$branch->timezone] ?? $branch->timezone }}</p>
                            <a href="{{ route('branches.edit', $branch) }}" class="app-btn-sm mt-4 px-4 py-2">Edit Cabang</a>
                        </div>
                    @empty
                        <div class="app-empty-state">Belum ada cabang yang terdaftar.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $branches->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
