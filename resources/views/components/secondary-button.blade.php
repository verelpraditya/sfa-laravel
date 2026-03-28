<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-2xl border border-sky-200 bg-sky-50 px-5 py-3 font-semibold text-sky-900 shadow-sm shadow-sky-100/80 transition hover:border-sky-300 hover:bg-sky-100 focus:outline-none focus:ring-4 focus:ring-sky-100 disabled:cursor-not-allowed disabled:opacity-25']) }}>
    {{ $slot }}
</button>
