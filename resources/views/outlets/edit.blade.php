<x-app-layout>
    <x-slot name="header">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Master Outlet</p>
                    <h2 class="mt-2 text-3xl font-semibold leading-tight text-slate-900">Edit Outlet</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $outlet->name }} · {{ $outlet->branch?->name }}</p>
                </div>
                <div class="rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-slate-600">
                    Dibuat oleh {{ $outlet->creator?->name ?? '-' }}
                </div>
            </div>
    </x-slot>

    <div class="py-5 sm:py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="app-panel p-5 sm:p-6">
                <form method="POST" action="{{ route('outlets.update', $outlet) }}">
                    @csrf
                    @method('PUT')
                    @include('outlets._form', ['submitLabel' => 'Update Outlet'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
