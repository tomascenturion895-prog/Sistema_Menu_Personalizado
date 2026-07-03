<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Encabezado con boton para abrir el modal de creacion --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <span class="eyebrow">// admin / categorías</span>
            <h3 class="text-2xl font-semibold text-gray-900">Categorías del menú</h3>
        </div>

        <x-primary-button wire:click="abrirModalCrear">
            + Nueva categoría
        </x-primary-button>
    </div>

    {{-- Tabla con las categorias existentes --}}
    <div class="tarjeta overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            {{-- Cabecera de tabla oscura, estilo terminal, consistente con la navbar --}}
            <thead class="bg-terminal-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Tipo de dieta</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-terminal-300 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                {{-- wire:key es obligatorio en los loops: le permite a Livewire identificar cada fila --}}
                @foreach ($categorias as $categoria)
                    <tr wire:key="categoria-{{ $categoria->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $categoria->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ $categoria->tipo_dieta }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{-- badge-activo / badge-inactivo son clases propias definidas en app.css --}}
                            @if ($categoria->activo)
                                <span class="badge-activo">Activa</span>
                            @else
                                <span class="badge-inactivo">Inactiva</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                            <button wire:click="abrirModalEditar({{ $categoria->id }})" class="text-brand-600 hover:text-brand-800">Editar</button>
                            <button wire:click="confirmarEliminar({{ $categoria->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $categorias->links() }}
        </div>
    </div>

    {{-- Modal de formulario, compartido para crear y editar. :show reabre el modal
         automaticamente si hay errores de validacion tras un submit fallido --}}
    <x-modal name="categoria-form" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $categoriaId ? 'Editar categoría' : 'Nueva categoría' }}
            </h2>

            <div class="mt-4">
                <x-input-label for="nombre" value="Nombre" />
                <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="descripcion" value="Descripción" />
                <textarea wire:model="descripcion" id="descripcion" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="tipo_dieta" value="Tipo de dieta" />
                <select wire:model="tipo_dieta" id="tipo_dieta" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    {{-- Las opciones salen del modelo: si se agrega una dieta nueva, se actualiza en un solo lugar --}}
                    @foreach (\App\Models\Categoria::TIPOS_DIETA as $valor => $etiqueta)
                        <option value="{{ $valor }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('tipo_dieta')" class="mt-2" />
            </div>

            <div class="mt-4 flex items-center">
                <input wire:model="activo" id="activo" type="checkbox" class="rounded border-gray-300">
                <label for="activo" class="ml-2 text-sm text-gray-600">Categoría activa</label>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-primary-button class="ms-3">
                    Guardar
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal de confirmacion antes de eliminar --}}
    <x-modal name="categoria-confirmar-eliminar" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">¿Eliminar esta categoría?</h2>
            <p class="mt-1 text-sm text-gray-600">
                Esta acción no se puede deshacer. Los productos asociados también se eliminarán.
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="eliminar" class="ms-3">
                    Eliminar
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
