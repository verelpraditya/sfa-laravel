<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Admin Pusat</p>
                <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Master User</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7 text-slate-500">Kelola akun user, role, cabang, dan status aktif untuk semua tim operasional.</p>
            </div>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] px-5 py-3 text-sm font-semibold text-white shadow-[0_20px_40px_-18px_rgba(29,78,216,0.75)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_46px_-18px_rgba(29,78,216,0.9)]">Tambah User</a>
        </div>
    </x-slot>

    <div class="py-6 sm:py-7">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('status') }}</div>
            @endif

            <section class="app-panel p-5">
                <form method="GET" class="grid gap-3 md:grid-cols-5">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Cari user" />
                        <x-text-input id="search" name="search" class="mt-2 block w-full" :value="$filters['search']" placeholder="Nama, username, email" />
                    </div>
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">Semua</option>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" @selected($filters['role'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="branch_id" value="Cabang" />
                        <select id="branch_id" name="branch_id" class="mt-2 block w-full rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] focus:border-sky-400 focus:ring-4 focus:ring-sky-100">
                            <option value="">Semua</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected((int) $filters['branch_id'] === $branch->id)>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-5 flex flex-wrap gap-3">
                        <x-primary-button>Terapkan Filter</x-primary-button>
                        <a href="{{ route('users.index') }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Reset</a>
                    </div>
                </form>
            </section>

            <section class="app-panel p-5">
                <div class="hidden overflow-hidden rounded-[1.5rem] border border-slate-200 lg:block shadow-[0_18px_40px_-30px_rgba(15,23,42,0.28)]">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">User</th>
                                <th class="px-4 py-3 font-semibold">Role</th>
                                <th class="px-4 py-3 font-semibold">Cabang</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($users as $managedUser)
                                <tr>
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-slate-900">{{ $managedUser->name }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ '@'.$managedUser->username }}{{ $managedUser->email ? ' · '.$managedUser->email : '' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">{{ $managedUser->roleLabel() }}</td>
                                    <td class="px-4 py-4 text-slate-600">{{ $managedUser->branch?->name ?? 'Semua Cabang' }}</td>
                                    <td class="px-4 py-4"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $managedUser->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td class="px-4 py-4"><a href="{{ route('users.edit', $managedUser) }}" class="inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100">Edit</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Belum ada user yang terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3 lg:hidden">
                    @forelse ($users as $managedUser)
                        <div class="app-soft-panel p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $managedUser->name }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ '@'.$managedUser->username }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $managedUser->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <p class="mt-4 text-sm text-slate-600">{{ $managedUser->roleLabel() }} · {{ $managedUser->branch?->name ?? 'Semua Cabang' }}</p>
                            <a href="{{ route('users.edit', $managedUser) }}" class="mt-4 inline-flex items-center rounded-2xl border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-900 shadow-sm shadow-sky-100/80">Edit User</a>
                        </div>
                    @empty
                        <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-500">Belum ada user yang terdaftar.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $users->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
