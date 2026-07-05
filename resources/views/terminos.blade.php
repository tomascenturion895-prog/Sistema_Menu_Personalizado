{{-- Pagina publica y estatica: no necesita controlador ni modelo, por eso se
     registra con Route::view (mismo criterio que la ruta "perfil"). El registro
     enlaza aca desde el checkbox de "Acepto los terminos y condiciones". --}}
<x-app-layout>
    <x-slot name="titulo">Términos y condiciones — Capa8Burger</x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <span class="eyebrow">// legal</span>
        <h1 class="font-display text-4xl sm:text-5xl uppercase text-terminal-950">Términos y condiciones</h1>
        <p class="text-gray-500 text-sm mt-2">Última actualización: {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>

        <div class="prose prose-terminal max-w-none mt-8 space-y-6 text-gray-700 leading-relaxed">
            <section>
                <h2 class="font-semibold text-terminal-950 text-lg border-t-2 border-terminal-950 pt-2">1. Uso de la cuenta</h2>
                <p class="mt-2">
                    Al registrarte en Capa8Burger declarás que los datos que cargaste (nombre, apellido,
                    correo y teléfono) son reales y te pertenecen. Sos responsable de mantener tu
                    contraseña en privado y de toda actividad realizada desde tu cuenta.
                </p>
            </section>

            <section>
                <h2 class="font-semibold text-terminal-950 text-lg border-t-2 border-terminal-950 pt-2">2. Pedidos</h2>
                <p class="mt-2">
                    Los pedidos realizados a través del sitio se retiran en el local. El teléfono
                    cargado en tu cuenta puede ser utilizado para contactarte ante cualquier
                    consulta sobre tu pedido.
                </p>
            </section>

            <section>
                <h2 class="font-semibold text-terminal-950 text-lg border-t-2 border-terminal-950 pt-2">3. Datos personales</h2>
                <p class="mt-2">
                    Tus datos se usan únicamente para gestionar tu cuenta y tus pedidos dentro de
                    este sistema. No se comparten con terceros.
                </p>
            </section>

            <section>
                <h2 class="font-semibold text-terminal-950 text-lg border-t-2 border-terminal-950 pt-2">4. Proyecto académico</h2>
                <p class="mt-2">
                    Capa8Burger es un proyecto desarrollado para la Tecnicatura Universitaria en
                    Programación (UTN) con fines educativos.
                </p>
            </section>
        </div>
    </div>
</x-app-layout>
