@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-200/90 bg-white px-4 py-3 text-sm text-slate-700 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:ring-4 focus:ring-sky-100']) }}>
