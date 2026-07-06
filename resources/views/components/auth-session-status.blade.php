@props(['status'])

{{-- Mismo criterio que <x-mensaje-flash>: se cierra solo a los 5s, o antes con la cruz --}}
@if ($status)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        x-init="setTimeout(() => visible = false, 5000)"
        x-transition
        {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 flex items-center gap-3']) }}>
        <span>{{ $status }}</span>
        <button type="button" @click="visible = false" class="text-green-600 hover:text-green-800" aria-label="Cerrar mensaje">
            &times;
        </button>
    </div>
@endif
