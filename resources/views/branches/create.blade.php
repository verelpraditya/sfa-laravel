<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Admin Pusat</p>
            <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Tambah Cabang</h2>
        </div>
    </x-slot>

    <div class="py-5 sm:py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="app-panel p-5 sm:p-6">
                <form method="POST" action="{{ route('branches.store') }}">
                    @csrf
                    @include('branches._form', ['submitLabel' => 'Simpan Cabang'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
