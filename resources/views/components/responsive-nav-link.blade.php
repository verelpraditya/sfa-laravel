@props(['active'])

@php
$classes = ($active ?? false)
            ? 'group block w-full rounded-[1.2rem] border border-sky-300/25 bg-[linear-gradient(135deg,rgba(103,232,249,0.2),rgba(37,99,235,0.2))] px-4 py-3 text-start text-sm font-semibold text-white shadow-[0_14px_34px_-22px_rgba(14,165,233,0.6)] shadow-sky-950/30 ring-1 ring-white/8'
            : 'group block w-full rounded-[1.2rem] px-4 py-3 text-start text-sm font-medium text-slate-200 transition duration-200 hover:bg-white/10 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
