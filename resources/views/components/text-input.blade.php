@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-control border-secondary-subtle rounded-3 shadow-none']) }}>