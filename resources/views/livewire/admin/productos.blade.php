<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <span class="eyebrow">// admin / productos</span>
            <h3 class="text-2xl font-semibold text-gray-900">Productos del menú</h3>
        </div>

        <x-primary-button wire:click="abrirModalCrear">
            + Nuevo producto
        </x-primary-button>
    </div>

    {{-- Esta tabla relaciona dos modelos: cada fila de Producto muestra el nombre
         de su Categoria (via la relacion belongsTo cargada con with('categoria')) --}}
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            {{-- Cabecera de tabla oscura, estilo terminal, consistente con la navbar --}}
            <thead class="bg-terminal-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-terminal-300 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($productos as $producto)
                    <tr wire:key="producto-{{ $producto->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $producto->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $producto->categoria->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><span class="precio">${{ number_format($producto->precio, 0, ',', '.') }}</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($producto->activo)
                                <span class="badge-activo">Activo</span>
                            @else
                                <span class="badge-inactivo">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                            <button wire:click="abrirModalEditar({{ $producto->id }})" class="text-brand-600 hover:text-brand-800">Editar</button>
                            <button wire:click="confirmarEliminar({{ $producto->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $productos->links() }}
        </div>
    </div>

    <x-modal name="producto-form" :show="$errors->isNotEmpty()" max-width="2xl" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $productoId ? 'Editar producto' : 'Nuevo producto' }}
            </h2>

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="categoria_id" value="Categoría" />
                    <select wire:model="categoria_id" id="categoria_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Seleccionar...</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="precio" value="Precio" />
                    <x-text-input wire:model="precio" id="precio" class="block mt-1 w-full" type="number" step="0.01" min="0" />
                    <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="nombre" value="Nombre" />
                <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="descripcion" value="Descripción" />
                <textarea wire:model="descripcion" id="descripcion" rows="2" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
            </div>

            {{-- Checkboxes de ingredientes, agrupados por tipo. Cada checkbox usa wire:model
                 sobre el mismo array "ingredientesSeleccionados": Livewire agrega/quita el id
                 del array automaticamente segun el checkbox este marcado o no --}}
            <div class="mt-4">
                <x-input-label value="Ingredientes disponibles para personalizar" />
                <div class="mt-2 grid grid-cols-2 gap-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                    @foreach ($ingredientes as $ingrediente)
                        <label class="flex items-center text-sm text-gray-700" wire:key="ingrediente-{{ $ingrediente->id }}">
                            <input type="checkbox" wire:model="ingredientesSeleccionados" value="{{ $ingrediente->id }}" class="rounded border-gray-300 mr-2">
                            {{ $ingrediente->nombre }}
                            <span class="text-gray-400 ml-1">({{ $ingrediente->tipo }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 flex items-center">
                <input wire:model="activo" id="activo" type="checkbox" class="rounded border-gray-300">
                <label for="activo" class="ml-2 text-sm text-gray-600">Producto activo</label>
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

    <x-modal name="producto-confirmar-eliminar" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">¿Eliminar este producto?</h2>
            <p class="mt-1 text-sm text-gray-600">Esta acción no se puede deshacer.</p>

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
