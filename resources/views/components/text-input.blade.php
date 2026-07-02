@props(['disabled' => false])

{{-- Input de texto estandar: el foco usa el naranja de marca en vez del indigo default de Breeze --}}
<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm']) }}>
