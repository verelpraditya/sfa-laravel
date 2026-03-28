@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-2xl bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700 shadow-sm'
            : 'inline-flex items-center rounded-2xl px-4 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-700';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
