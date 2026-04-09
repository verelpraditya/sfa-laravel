<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-[3rem] items-center justify-center gap-2 rounded-[0.95rem] bg-[linear-gradient(135deg,#e11d48_0%,#be123c_100%)] px-5 py-3 text-[14px] font-semibold text-white shadow-[0_18px_36px_-18px_rgba(225,29,72,0.5)] transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_22px_42px_-18px_rgba(225,29,72,0.65)] focus:outline-none focus:ring-4 focus:ring-rose-100 disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
