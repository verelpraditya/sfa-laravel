<button {{ $attributes->merge(['type' => 'submit', 'class' => 'app-action-danger']) }}>
    {{ $slot }}
</button>
