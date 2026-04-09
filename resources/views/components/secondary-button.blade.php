<button {{ $attributes->merge(['type' => 'button', 'class' => 'app-action-secondary']) }}>
    {{ $slot }}
</button>
