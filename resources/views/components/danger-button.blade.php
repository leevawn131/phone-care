<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger rounded-3 px-4']) }}>
    {{ $slot }}
</button>
