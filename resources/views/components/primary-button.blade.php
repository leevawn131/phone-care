<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary rounded-3 px-4']) }}>
    {{ $slot }}
</button>