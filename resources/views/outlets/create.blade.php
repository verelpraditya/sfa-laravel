<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Master Outlet</p>
            <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Tambah Outlet Baru</h2>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60 sm:p-6">
                <form method="POST" action="{{ route('outlets.store') }}">
                    @csrf
                    @include('outlets._form', ['submitLabel' => 'Simpan Outlet'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
