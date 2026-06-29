<div>
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">Ingredientes</h3>

        <x-primary-button wire:click="abrirModalCrear">
            + Nuevo ingrediente
        </x-primary-button>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio extra</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dieta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($ingredientes as $ingrediente)
                    <tr wire:key="ingrediente-{{ $ingrediente->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ingrediente->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ $ingrediente->tipo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${{ number_format($ingrediente->precio_extra, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-xs space-x-1">
                            @if ($ingrediente->es_vegetariano)
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Vegetariano</span>
                            @endif
                            @if ($ingrediente->es_vegano)
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-green-100 text-green-800">Vegano</span>
                            @endif
                            @if ($ingrediente->sin_gluten)
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">Sin gluten</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($ingrediente->activo)
                                <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                            @else
                                <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                            <button wire:click="abrirModalEditar({{ $ingrediente->id }})" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                            <button wire:click="confirmarEliminar({{ $ingrediente->id }})" class="text-red-600 hover:text-red-900">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-4">
            {{ $ingredientes->links() }}
        </div>
    </div>

    <x-modal name="ingrediente-form" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="guardar" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $ingredienteId ? 'Editar ingrediente' : 'Nuevo ingrediente' }}
            </h2>

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="nombre" value="Nombre" />
                    <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="precio_extra" value="Precio extra" />
                    <x-text-input wire:model="precio_extra" id="precio_extra" class="block mt-1 w-full" type="number" step="0.01" min="0" />
                    <x-input-error :messages="$errors->get('precio_extra')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="tipo" value="Tipo" />
                <select wire:model="tipo" id="tipo" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    <option value="pan">Pan</option>
                    <option value="medallon">Medallón</option>
                    <option value="topping">Topping</option>
                    <option value="salsa">Salsa</option>
                    <option value="papas">Papas</option>
                    <option value="bebida">Bebida</option>
                    <option value="extra">Extra</option>
                </select>
                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2">
                <label class="flex items-center text-sm text-gray-600">
                    <input wire:model="es_vegetariano" type="checkbox" class="rounded border-gray-300 mr-2">
                    Vegetariano
                </label>
                <label class="flex items-center text-sm text-gray-600">
                    <input wire:model="es_vegano" type="checkbox" class="rounded border-gray-300 mr-2">
                    Vegano
                </label>
                <label class="flex items-center text-sm text-gray-600">
                    <input wire:model="sin_gluten" type="checkbox" class="rounded border-gray-300 mr-2">
                    Sin gluten
                </label>
            </div>

            <div class="mt-4 flex items-center">
                <input wire:model="activo" id="activo" type="checkbox" class="rounded border-gray-300">
                <label for="activo" class="ml-2 text-sm text-gray-600">Ingrediente activo</label>
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

    <x-modal name="ingrediente-confirmar-eliminar" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">¿Eliminar este ingrediente?</h2>
            <p class="mt-1 text-sm text-gray-600">
                Esta acción no se puede deshacer. Se quitará de todos los productos que lo tengan asociado.
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
