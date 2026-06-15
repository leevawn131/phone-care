<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-secondary rounded-3 px-4']) }}>
    {{ $slot }}
</button>