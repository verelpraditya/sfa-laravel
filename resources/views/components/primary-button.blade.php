<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#1d4ed8_0%,#0f172a_100%)] px-5 py-3 font-semibold text-white shadow-[0_20px_40px_-18px_rgba(29,78,216,0.75)] transition hover:-translate-y-0.5 hover:shadow-[0_24px_46px_-18px_rgba(29,78,216,0.9)] focus:outline-none focus:ring-4 focus:ring-sky-100 disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
