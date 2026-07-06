<?php

namespace App\Livewire\Concerns;

use Livewire\Attributes\Locked;

/**
 * Mensaje flash de exito (agregar al carrito, confirmar, cancelar...), como
 * propiedad del PROPIO componente en vez de un componente Livewire aparte.
 *
 * Se probo con un <livewire:mensaje-flash /> nested aparte, escuchando un
 * evento dispatch()-eado desde el componente que agrega al carrito. Fallaba
 * de forma intermitente: el metodo del evento SI se ejecutaba (confirmado
 * con logs) y mutaba la propiedad, pero la respuesta que Livewire mandaba al
 * navegador para ESE componente hijo no siempre reflejaba el cambio -un
 * problema de fondo con el orden de commit de Livewire para listeners de
 * eventos entre componentes distintos, dificil de diagnosticar mas a fondo-.
 *
 * La solucion mas simple y confiable: nada de comunicacion entre
 * componentes. El mensaje vive como propiedad de ESTE MISMO componente
 * (Index, MiPedido, Admin\Pedidos), y el wire:click/wire:poll del mensaje
 * apuntan directo a el, sin cruzar ningun limite de componente.
 */
trait ConMensajeFlash
{
    #[Locked]
    public ?string $mensajeFlash = null;

    /**
     * Cada componente que usa este trait debe llamar a esto en su propio
     * mount() para recuperar un mensaje flasheado desde un redirect (ej.
     * PedidoController::repetir(), una accion de OTRO componente que
     * redirige a este via $this->redirect()).
     */
    public function recuperarMensajeFlash(): void
    {
        $this->mensajeFlash = session('mensaje');
    }

    /**
     * Muestra un mensaje sin salir de la pagina actual: usar esto en vez de
     * session()->flash('mensaje', ...) cuando la accion NO hace un redirect
     * (ej. agregar un combo al carrito desde el menu).
     */
    public function mostrarMensajeFlash(string $mensaje): void
    {
        $this->mensajeFlash = $mensaje;
    }

    public function ocultarMensajeFlash(): void
    {
        $this->mensajeFlash = null;
    }
}
