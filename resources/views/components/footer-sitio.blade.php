{{-- Footer persistente del area logueada: garantiza que la info del negocio
     (direccion, horario, contacto) siga siendo alcanzable aunque el usuario
     nunca vuelva a ver la landing publica una vez que inicio sesion.
     Los datos salen de config/negocio.php (misma fuente que usa welcome.blade.php). --}}
<footer class="bg-terminal-950 text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-6">
            <div class="border-t-2 border-white/20 pt-3">
                <h3 class="font-mono text-xs uppercase tracking-wider text-brand-400">Ubicación</h3>
                <p class="text-sm text-terminal-300 mt-1.5 leading-relaxed">
                    {{ config('negocio.direccion') }}<br>
                    {{ config('negocio.ciudad') }}
                </p>
            </div>

            <div class="border-t-2 border-white/20 pt-3">
                <h3 class="font-mono text-xs uppercase tracking-wider text-brand-400">Horarios</h3>
                <p class="text-sm text-terminal-300 mt-1.5 leading-relaxed">
                    {{ config('negocio.horario_dias') }}<br>
                    {{ config('negocio.horario_horas') }}
                </p>
            </div>

            <div class="border-t-2 border-white/20 pt-3">
                <h3 class="font-mono text-xs uppercase tracking-wider text-brand-400">Contacto</h3>
                <p class="text-sm text-terminal-300 mt-1.5 leading-relaxed">
                    Tel: {{ config('negocio.telefono') }}<br>
                    {{ config('negocio.email') }}
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-8 pt-6 border-t border-terminal-800">
            <x-application-logo class="text-sm text-white" />

            {{-- Vuelve a la landing publica: el unico lugar donde antes vivia toda esta info --}}
            <a href="{{ route('home') }}#contacto" class="text-sm font-semibold text-brand-400 hover:text-brand-300 hover:underline underline-offset-4">
                Conocé más sobre nosotros →
            </a>
        </div>
    </div>
</footer>
