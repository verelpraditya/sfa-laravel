@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center rounded-[1.15rem] border border-sky-300/20 bg-[linear-gradient(135deg,rgba(56,189,248,0.24),rgba(37,99,235,0.18))] px-4 py-3 text-sm font-semibold text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.08)]'
            : 'flex items-center rounded-[1.15rem] px-4 py-3 text-sm font-medium text-slate-300 transition hover:bg-white/8 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
