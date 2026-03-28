<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-2xl bg-rose-600 px-5 py-3 font-semibold text-white shadow-lg shadow-rose-600/15 transition hover:bg-rose-500 focus:outline-none focus:ring-4 focus:ring-rose-100']) }}>
    {{ $slot }}
</button>
