@props(['on'])

{{-- Antes se cerraba solo a los 2s sin forma de cerrarlo antes; ahora dura 5s
     (mismo criterio que <x-mensaje-flash>) y se puede cerrar con la cruz --}}
<div x-data="{ shown: false, timeout: null }"
     x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 5000); })"
     x-show.transition.out.opacity.duration.1500ms="shown"
     x-transition:leave.opacity.duration.1500ms
     style="display: none;"
    {{ $attributes->merge(['class' => 'text-sm text-gray-600 inline-flex items-center gap-2']) }}>
    <span>{{ $slot->isEmpty() ? __('Saved.') : $slot }}</span>
    <button type="button" @click="clearTimeout(timeout); shown = false" class="text-gray-400 hover:text-gray-600" aria-label="Cerrar mensaje">
        &times;
    </button>
</div>
