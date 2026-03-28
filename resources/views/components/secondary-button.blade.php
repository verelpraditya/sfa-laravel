<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-2xl border border-slate-200 bg-white px-5 py-3 font-semibold text-slate-700 shadow-sm shadow-slate-200/60 transition hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-brand-100 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
