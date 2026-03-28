<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Admin Pusat</p>
            <h2 class="mt-2 text-3xl font-semibold leading-tight text-ink-950">Edit Cabang</h2>
            <p class="mt-2 text-sm text-slate-500">{{ $branch->name }} · {{ $branch->code }}</p>
        </div>
    </x-slot>

    <div class="py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('status') }}</div>
            @endif

            <div class="rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-sm shadow-slate-200/60 sm:p-6">
                <form method="POST" action="{{ route('branches.update', $branch) }}">
                    @csrf
                    @method('PUT')
                    @include('branches._form', ['submitLabel' => 'Update Cabang'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
