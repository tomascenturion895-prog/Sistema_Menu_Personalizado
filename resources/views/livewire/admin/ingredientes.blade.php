<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <span class="eyebrow">// admin / ingredientes</span>
            <h3 class="text-2xl font-semibold text-gray-900">Ingredientes</h3>
        </div>

        <x-primary-button wire:click="abrirModalCrear">
            + Nuevo ingrediente
        </x-primary-button>
    </div>

    {{-- overflow-x-auto: en pantallas chicas la tabla se scrollea horizontal en vez de cortarse --}}
    <div class="tarjeta overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            {{-- Cabecera de tabla oscura, estilo terminal, consistente con la navbar --}}
            <thead class="bg-terminal-950">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Precio extra</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Dieta</th>
                    <th class="px-6 py-3 text-left text-xs font-mono font-medium text-terminal-300 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-mono font-medium text-terminal-300 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($ingredientes as $ingrediente)
                    <tr wire:key="ingrediente-{{ $ingrediente->id }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ingrediente->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">{{ $ingrediente->tipo }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm"><span class="precio">@precio($ingrediente->precio_extra)</span></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{-- Sin stock resalta en rojo: es lo primero que el admin necesita notar --}}
                            <span class="font-mono font-semibold {{ $ingrediente->sinStock() ? 'text-tomate-600' : 'text-terminal-950' }}">
                                {{ $ingrediente->stock }}
                            </span>
                            @if ($ingrediente->sinStock())
                                <span class="badge bg-tomate-100 text-tomate-700 ml-1">Sin stock</span>
                            @endif
                        </td>
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
                                <span class="badge-activo">Activo</span>
                            @else
                                <span class="badge-inactivo">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                            <button wire:click="abrirModalEditar({{ $ingrediente->id }})" class="text-brand-600 hover:text-brand-800">Editar</button>
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
            {{-- Mismo lenguaje visual que el resto del panel: eyebrow + titulo editorial,
                 en vez del "Nuevo ingrediente" generico que traia el modal --}}
            <span class="eyebrow">// {{ $ingredienteId ? 'editar' : 'nuevo' }} ingrediente</span>
            <h2 class="font-display text-2xl uppercase text-terminal-950">
                {{ $ingredienteId ? 'Editar ingrediente' : 'Nuevo ingrediente' }}
            </h2>

            <div class="mt-5 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="nombre" value="Nombre" />
                    <x-text-input wire:model="nombre" id="nombre" class="block mt-1 w-full" type="text" />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="precio_extra" value="Precio extra" />
                    {{-- Prefijo "$": se manejan pesos argentinos, igual que @precio() en toda la app --}}
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 font-mono text-sm">$</span>
                        <x-text-input wire:model="precio_extra" id="precio_extra" class="block w-full pl-7" type="number" step="0.01" min="0" />
                    </div>
                    <x-input-error :messages="$errors->get('precio_extra')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="stock" value="Stock disponible (unidades)" />
                <x-text-input wire:model.live="stock" id="stock" class="block mt-1 w-full sm:w-1/2" type="number" step="1" min="0" />
                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="tipo" value="Tipo" />
                <select wire:model="tipo" id="tipo" class="block mt-1 w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                    {{-- Los tipos salen del modelo: agregar uno nuevo se hace en un solo lugar --}}
                    @foreach (\App\Models\Ingrediente::TIPOS as $valor => $etiqueta)
                        <option value="{{ $valor }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
            </div>

            {{-- Regla superior, como en el resto de las secciones editoriales del sistema:
                 separa visualmente el bloque de "dieta" del resto del formulario --}}
            <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-3 gap-2">
                <label class="flex items-center text-sm text-gray-600">
                    <input wire:model="es_vegetariano" type="checkbox" class="rounded border-gray-300 accent-brand-500 mr-2">
                    Vegetariano
                </label>
                <label class="flex items-center text-sm text-gray-600">
                    <input wire:model="es_vegano" type="checkbox" class="rounded border-gray-300 accent-brand-500 mr-2">
                    Vegano
                </label>
                <label class="flex items-center text-sm text-gray-600">
                    <input wire:model="sin_gluten" type="checkbox" class="rounded border-gray-300 accent-brand-500 mr-2">
                    Sin gluten
                </label>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                {{-- Sin stock, el ingrediente se guarda inactivo si o si (regla de negocio en
                     el modelo): el checkbox se deshabilita para no prometer algo que no va a pasar --}}
                <label class="flex items-center {{ (int) $stock <= 0 ? 'opacity-50' : '' }}">
                    <input wire:model="activo" id="activo" type="checkbox" class="rounded border-gray-300 accent-brand-500" @disabled((int) $stock <= 0)>
                    <span class="ml-2 text-sm text-gray-600">Ingrediente activo</span>
                </label>

                @if ((int) $stock <= 0)
                    <p class="text-xs text-tomate-600 mt-1.5">
                        Sin stock: se va a guardar como <strong>inactivo</strong> automáticamente.
                    </p>
                @endif
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-primary-button class="ms-3" wire:loading.attr="disabled" wire:target="guardar">
                    Guardar
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="ingrediente-confirmar-eliminar" focusable>
        <div class="p-6">
            <span class="eyebrow">// eliminar</span>
            <h2 class="font-display text-xl uppercase text-terminal-950">¿Eliminar este ingrediente?</h2>
            <p class="mt-2 text-sm text-gray-600">
                Esta acción no se puede deshacer. Se quitará de todos los productos que lo tengan asociado.
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="eliminar" class="ms-3" wire:loading.attr="disabled" wire:target="eliminar">
                    Eliminar
                </x-danger-button>
            </div>
        </div>
    </x-modal>
</div>
