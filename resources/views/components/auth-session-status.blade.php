@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium']) }}>
        {{ $status }}
    </div>
@endif
