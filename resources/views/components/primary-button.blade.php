<button {{ $attributes->merge(['type' => 'submit', 'class' => 'app-action-primary']) }}>
    {{ $slot }}
</button>
