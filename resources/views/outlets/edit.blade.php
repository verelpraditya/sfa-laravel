<x-app-layout>
    <x-slot name="header">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Master Outlet</p>
                    <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Edit Outlet</h2>
                    <p class="mt-2 text-sm text-slate-500">{{ $outlet->name }} · {{ $outlet->branch?->name }}</p>
                </div>
                <div class="rounded-2xl border border-sky-100 bg-sky-50/80 px-4 py-3 text-sm text-slate-600">
                    Dibuat oleh {{ $outlet->creator?->name ?? '-' }}
                </div>
            </div>
    </x-slot>

    <div class="py-5 sm:py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

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
