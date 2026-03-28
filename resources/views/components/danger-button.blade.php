<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#e11d48_0%,#be123c_100%)] px-5 py-3 font-semibold text-white shadow-[0_18px_36px_-18px_rgba(225,29,72,0.75)] transition hover:-translate-y-0.5 hover:shadow-[0_22px_42px_-18px_rgba(225,29,72,0.9)] focus:outline-none focus:ring-4 focus:ring-rose-100']) }}>
    {{ $slot }}
</button>
