@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'app-alert app-alert-success']) }}>
        <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/80 text-emerald-600 shadow-sm">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="m5 13 4 4L19 7" /></svg>
        </span>
        <div class="min-w-0">
            <p class="text-[12px] font-semibold uppercase tracking-[0.14em] text-emerald-700">Info</p>
            <p class="mt-1 font-medium">{{ $status }}</p>
        </div>
    </div>
@endif
