@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-2xl bg-brand-50 px-4 py-3 text-start text-sm font-semibold text-brand-700 shadow-sm'
            : 'block w-full rounded-2xl px-4 py-3 text-start text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
