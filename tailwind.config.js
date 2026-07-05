import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // ============================================================
            // TOKENS DE COLOR — tema Capa8Burger
            // Hamburgueseria + programacion: colores calidos de parrilla
            // combinados con una paleta oscura estilo terminal de codigo.
            // Usar SIEMPRE estos tokens en vez de colores sueltos de Tailwind
            // (ej: bg-brand-500, NO bg-orange-500) para mantener consistencia.
            // ============================================================
            colors: {
                // Color principal de la marca: naranja "a la parrilla".
                // Escala completa 50 (mas claro) a 950 (mas oscuro), igual que las de Tailwind.
                brand: {
                    // Escala suavizada (menos saturada que un naranja puro tipo "ff7f11",
                    // pero con mas fuerza que un primer intento demasiado palido/lavado):
                    // sigue siendo un naranja "a la parrilla" con caracter. Un solo cambio
                    // aca afecta a toda la app (botones, tabs activas, hero, badges).
                    50: '#fff7ed',
                    100: '#ffecd1',
                    200: '#fed3a0',
                    300: '#fdb168',
                    400: '#fa8f3f',
                    500: '#f37522', // <- tono base de la marca (botones, links, tabs activas)
                    600: '#dd5d10',
                    700: '#b8470c',
                    800: '#933a14',
                    900: '#773014',
                    950: '#421c0f',
                },

                // Paleta oscura "terminal": para navbar, footer, panel admin y fondos oscuros.
                // Inspirada en editores de codigo (VS Code dark).
                terminal: {
                    50: '#f4f6f7',
                    100: '#e3e7ea',
                    200: '#cad2d7',
                    300: '#a5b2bb',
                    400: '#798a97',
                    500: '#5e6f7c',
                    600: '#515e69',
                    700: '#464f58',
                    800: '#3e454c',
                    900: '#1e2227', // <- fondo principal oscuro (como un editor)
                    950: '#14171b',
                },

                // Verde "codigo compilado": exitos, confirmaciones y el acento hacker del tema.
                // Tambien es el color de "lechuga" — doble proposito burger/codigo.
                exito: {
                    100: '#dcfce9',
                    500: '#22c55e',
                    700: '#15803d',
                },

                // Colores semanticos para los tipos de dieta (badges y filtros del menu).
                // Nombrados por significado, no por color, para poder cambiarlos sin tocar las vistas.
                dieta: {
                    vegetariano: '#4ade80', // verde claro
                    vegano: '#16a34a',      // verde intenso
                    celiaco: '#facc15',     // amarillo trigo (sin gluten)
                    normal: '#fb923c',      // naranja suave
                },

                // Amarillo cheddar: destacados, precios y promociones.
                cheddar: {
                    100: '#fef9c3',
                    400: '#facc15',
                    500: '#eab308',
                },

                // Rojo tomate: errores, validaciones y acciones destructivas (eliminar).
                tomate: {
                    100: '#fee2e2',
                    500: '#ef4444',
                    700: '#b91c1c',
                },
            },

            // ============================================================
            // TOKENS DE TIPOGRAFIA
            // display -> titulos grandes de la marca (gruesa, estilo cartel de hamburgueseria)
            // sans    -> texto general (Figtree, ya venia con Breeze)
            // mono    -> acento "de programador": precios, badges, codigos de pedido
            // ============================================================
            fontFamily: {
                display: ['Archivo Black', ...defaultTheme.fontFamily.sans],
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },

            // ============================================================
            // OTROS TOKENS REUTILIZABLES
            // ============================================================

            // Sombras duras desplazadas, estilo cartel retro: son LA sombra del sistema
            // (tarjetas .tarjeta, botones .btn-retro, stickers de la landing)
            boxShadow: {
                retro: '4px 4px 0 0 #14171b',
                'retro-sm': '2px 2px 0 0 #14171b',
            },
        },
    },

    plugins: [forms],
};
